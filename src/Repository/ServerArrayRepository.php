<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

use IntoTheVoid\Env\Exception\Repository\InvalidName;
use IntoTheVoid\Env\Helper\Str;

class ServerArrayRepository implements RepositoryInterface
{
    public function get(string $name): string|null
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        if (! isset($_SERVER[$name])) {
            return null;
        }

        return Str::from($_SERVER[$name]);
    }

    public function set(string $name, string|null $value): void
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        if ($value === null) {
            unset($_SERVER[$name]);

            return;
        }

        $_SERVER[$name] = $value;
    }
}
