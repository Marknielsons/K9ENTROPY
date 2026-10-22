<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use K9Entropy\SignalGenerator;
use K9Entropy\SignalStore;
use K9Entropy\SignalValidator;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');

function respond(int $code, array $json): never
{
    http_response_code($code);
    echo json_encode($json, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$store = new SignalStore();

if ($path === '/api/health' && $method === 'GET') {
    respond(200, ['ok' => true, 'project' => 'K9ENTROPY', 'classification' => 'research-prototype']);
}
if ($path === '/api/status' && $method === 'GET') {
    $incoming = $store->readFresh();
    $isLive = $incoming !== null;
    $channels = $isLive ? $incoming['channels'] : SignalGenerator::generate();
    respond(200, [
        'project' => 'K9ENTROPY',
        'source' => $isLive ? 'adapter_unverified' : 'simulation',
        'source_label' => $isLive ? 'EEG ADAPTER · UNVERIFIED' : 'SYNTHETIC DATA · DEMO',
        'received_at' => gmdate('c'),
        'signal' => [
            'channels' => $channels,
            'channel_count' => count($channels),
            'samples_per_channel' => count(reset($channels)),
            'mean_absolute_amplitude_uv' => SignalGenerator::averageAmplitude($channels),
        ],
        'security' => [
            'neural_entropy_credited_bits' => 0,
            'brain_data_used_for_keys' => false,
            'random_source' => 'PHP random_bytes / OS CSPRNG',
            'validated_neural_entropy' => false,
            'certified' => false,
            'production_ready' => false,
        ],
    ]);
}
if ($path === '/api/random' && $method === 'GET') {
    $value = $_GET['bytes'] ?? '16';
    if (!is_string($value) || !preg_match('/^(?:[1-9]|[1-5][0-9]|6[0-4])$/D', $value)) {
        respond(400, ['error' => 'bytes must be an integer from 1 to 64']);
    }
    $bytes = (int)$value;
    respond(200, [
        'hex' => bin2hex(random_bytes($bytes)),
        'bytes' => $bytes,
        'source' => 'operating_system_csprng',
        'brain_data_used' => false,
        'purpose' => 'demonstration_only',
        'warning' => 'Public API output is not a private secret. Never use this endpoint to generate passwords or encryption keys.',
    ]);
}
if ($path === '/api/eeg' && $method === 'POST') {
    // Local-only prototype ingestion. Never place a token in client-side JS.
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
        respond(403, ['error' => 'Localhost only']);
    }
    $token = getenv('K9_INGEST_TOKEN');
    if ($token === false || strlen($token) < 24) {
        respond(503, ['error' => 'Set a K9_INGEST_TOKEN of at least 24 characters on the server']);
    }
    $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($authorization, 'Bearer ') || !hash_equals($token, substr($authorization, 7))) {
        respond(401, ['error' => 'Unauthorized']);
    }
    $body = file_get_contents('php://input', false, null, 0, 65537);
    if ($body === false || strlen($body) > 65536) {
        respond(413, ['error' => 'Payload exceeds 64 KB']);
    }
    try {
        $payload = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
        $channels = SignalValidator::validate($payload);
        $store->write($channels);
    } catch (\JsonException | \InvalidArgumentException $e) {
        respond(422, ['error' => $e->getMessage()]);
    } catch (\Throwable $e) {
        error_log('EEG ingestion error: ' . $e->getMessage());
        respond(500, ['error' => 'Unable to save EEG frame']);
    }
    respond(202, ['accepted' => true, 'mode' => 'visualization_only', 'security_entropy_bits' => 0]);
}
respond(404, ['error' => 'No such API endpoint or method']);
