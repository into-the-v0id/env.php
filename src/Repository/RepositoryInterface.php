<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix

interface RepositoryInterface
{
    /** @return string|null Returns null if the variable does not exist */
    public function get(string $name): string|null;

    /** @param string|null $value Pass null to remove the variable */
    public function set(string $name, string|null $value): void;
}
