<?php

declare(strict_types=1);

namespace K9Entropy;

final class SignalGenerator
{
    /** Synthetic waveforms used only for UI development, never for entropy. */
    public static function generate(int $length = 96): array
    {
        $channels = [];
        $time = microtime(true);
        for ($channel = 1; $channel <= 4; $channel++) {
            $values = [];
            for ($i = 0; $i < $length; $i++) {
                $x = ($i / 18) + ($time * 2.4) + $channel;
                $v = sin($x * (1.7 + $channel * .09)) * (18 + $channel * 4)
                    + sin($x * 3.7 + $channel) * 5
                    + sin($x * 6.2 + $channel * 2) * 3
                    + random_int(-360, 360) / 100;
                $values[] = round($v, 2);
            }
            $channels['CH' . $channel] = $values;
        }
        return $channels;
    }

    /** Display-only, not a statistical entropy estimate. */
    public static function averageAmplitude(array $channels): float
    {
        $absoluteSum = 0;
        $count = 0;
        foreach ($channels as $samples) {
            foreach ($samples as $sample) {
                $absoluteSum += abs((float)$sample);
                $count++;
            }
        }
        return $count === 0 ? 0 : round($absoluteSum / $count, 1);
    }
}
