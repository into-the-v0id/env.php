<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

class NoopNormalizer implements NormalizerInterface
{
    public function normalize(string $value): string
    {
        return $value;
    }
}
