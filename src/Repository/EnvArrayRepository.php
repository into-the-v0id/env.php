<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

use IntoTheVoid\Env\Exception\Repository\InvalidName;
use IntoTheVoid\Env\Helper\Str;

class EnvArrayRepository implements RepositoryInterface
{
    public function get(string $name): ?string
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        if (! isset($_ENV[$name])) {
            return null;
        }

        return Str::from($_ENV[$name]);
    }

    public function set(string $name, ?string $value): void
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        if ($value === null) {
            unset($_ENV[$name]);

            return;
        }

        $_ENV[$name] = $value;
    }
}
