<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix

interface RepositoryInterface
{
    public function get(string $name): ?string;

    public function set(string $name, ?string $value): void;
}
