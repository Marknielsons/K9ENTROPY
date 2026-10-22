<?php

declare(strict_types=1);

namespace K9Entropy;

use InvalidArgumentException;

final class SignalValidator
{
    private const CHANNELS = ['CH1', 'CH2', 'CH3', 'CH4'];

    /**
     * Validate an untrusted EEG-adapter payload. Values are microvolt-scale
     * illustrative inputs, not proof of EEG authenticity or entropy.
     *
     * @return array<string, list<float>>
     */
    public static function validate(mixed $payload): array
    {
        if (!is_array($payload) || !isset($payload['channels']) || !is_array($payload['channels'])) {
            throw new InvalidArgumentException('Expected an object containing channels.');
        }
        $input = $payload['channels'];
        if (array_keys($input) !== self::CHANNELS) {
            throw new InvalidArgumentException('Exactly CH1, CH2, CH3 and CH4 are required in order.');
        }
        $length = null;
        $validated = [];
        foreach (self::CHANNELS as $name) {
            $values = $input[$name];
            if (!is_array($values) || !array_is_list($values)) {
                throw new InvalidArgumentException("$name must contain a numeric array.");
            }
            $count = count($values);
            if ($count < 32 || $count > 128 || ($length !== null && $length !== $count)) {
                throw new InvalidArgumentException('Channels must contain 32 to 128 samples with equal lengths.');
            }
            $length = $count;
            $validated[$name] = [];
            foreach ($values as $value) {
                if ((!is_float($value) && !is_int($value)) || !is_finite((float)$value) || abs((float)$value) > 1000) {
                    throw new InvalidArgumentException("$name contains an invalid sample.");
                }
                $validated[$name][] = round((float)$value, 4);
            }
        }
        return $validated;
    }
}
