<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

class NormalizerChain implements NormalizerInterface
{
    /** @param NormalizerInterface[] $normalizers */
    public function __construct(
        protected array $normalizers,
    ) {
    }

    public function normalize(string $value): string
    {
        foreach ($this->normalizers as $normalizer) {
            $value = $normalizer->normalize($value);
        }

        return $value;
    }
}
