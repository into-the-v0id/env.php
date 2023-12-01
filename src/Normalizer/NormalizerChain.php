<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

class NormalizerChain implements NormalizerInterface
{
    /** @var NormalizerInterface[] */
    protected $normalizers;

    /**
     * @param NormalizerInterface[] $normalizers
     */
    public function __construct(array $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function normalize(string $value): string
    {
        foreach ($this->normalizers as $normalizer) {
            $value = $normalizer->normalize($value);
        }

        return $value;
    }
}
