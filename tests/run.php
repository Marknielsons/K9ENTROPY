<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use K9Entropy\SignalGenerator;
use K9Entropy\SignalStore;
use K9Entropy\SignalValidator;

$successes = 0;
function check(bool $ok, string $label): void
{
    global $successes;
    if (!$ok) {
        fwrite(STDERR, "FAIL: $label\n");
        exit(1);
    }
    $successes++;
    echo "PASS: $label\n";
}

$channels = SignalGenerator::generate(64);
check(count($channels) === 4 && count($channels['CH1']) === 64, 'Simulator creates four channels');
$validated = SignalValidator::validate(['channels' => $channels]);
check($validated === $channels, 'Valid frames accepted');
check(SignalGenerator::averageAmplitude($channels) >= 0, 'Average amplitude works');

$invalid = ['channels' => $channels];
$invalid['channels']['CH2'][0] = 'not-a-number';
try {
    SignalValidator::validate($invalid);
    check(false, 'Invalid frame rejected');
} catch (InvalidArgumentException $e) {
    check(true, 'Invalid frame rejected');
}

$short = ['channels' => $channels];
$short['channels']['CH1'] = [1.0];
try {
    SignalValidator::validate($short);
    check(false, 'Short channel rejected');
} catch (InvalidArgumentException $e) {
    check(true, 'Short channel rejected');
}

$path = sys_get_temp_dir() . '/k9entropy-test-' . bin2hex(random_bytes(8)) . '.json';
$store = new SignalStore($path);
try {
    $store->write($channels);
    $stored = $store->readFresh();
    check($stored !== null && $stored['channels'] === $channels, 'Private signal buffer roundtrip');
} finally {
    @unlink($path);
}
check(strlen(random_bytes(32)) === 32, 'Operating system CSPRNG returns 32 bytes');
echo "\n$successes tests passed.\n";
