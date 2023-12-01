<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

use function trim;

class TrimNormalizer implements NormalizerInterface
{
    public function normalize(string $value): string
    {
        return trim($value);
    }
}
