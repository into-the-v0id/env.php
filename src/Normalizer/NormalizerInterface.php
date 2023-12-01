<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix

interface NormalizerInterface
{
    public function normalize(string $value): string;
}
