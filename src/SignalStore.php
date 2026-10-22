<?php

declare(strict_types=1);

namespace K9Entropy;

use RuntimeException;

final class SignalStore
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? dirname(__DIR__) . '/var/latest-eeg.json';
    }

    /** @param array<string, list<float>> $channels */
    public function write(array $channels): void
    {
        $folder = dirname($this->path);
        if (!is_dir($folder) && !mkdir($folder, 0700, true) && !is_dir($folder)) {
            throw new RuntimeException('Could not create private EEG buffer directory.');
        }
        $data = json_encode(['received_at' => microtime(true), 'channels' => $channels], JSON_THROW_ON_ERROR);
        $tmp = tempnam($folder, '.incoming-');
        if ($tmp === false) {
            throw new RuntimeException('Could not create temporary EEG buffer.');
        }
        try {
            chmod($tmp, 0600);
            if (file_put_contents($tmp, $data, LOCK_EX) === false || !rename($tmp, $this->path)) {
                throw new RuntimeException('Could not update EEG buffer.');
            }
        } finally {
            if (is_file($tmp)) {
                unlink($tmp);
            }
        }
    }

    /** @return array<string,mixed>|null */
    public function readFresh(int $ttlSeconds = 10): ?array
    {
        if (!is_file($this->path)) {
            return null;
        }
        $raw = @file_get_contents($this->path);
        if ($raw === false) {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['received_at'], $data['channels']) ||
            !is_numeric($data['received_at']) || microtime(true) - (float)$data['received_at'] > $ttlSeconds ||
            (float)$data['received_at'] > microtime(true) + 2) {
            return null;
        }
        try {
            $channels = SignalValidator::validate(['channels' => $data['channels']]);
        } catch (\InvalidArgumentException) {
            return null;
        }
        return ['received_at' => $data['received_at'], 'channels' => $channels];
    }
}
