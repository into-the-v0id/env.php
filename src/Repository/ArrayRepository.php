<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

use IntoTheVoid\Env\Helper\Str;

class ArrayRepository implements RepositoryInterface
{
    /** @param mixed[] $data */
    public function __construct(
        protected array $data,
    ) {
    }

    public function get(string $name): string|null
    {
        if (! isset($this->data[$name])) {
            return null;
        }

        return Str::from($this->data[$name]);
    }

    public function set(string $name, string|null $value): void
    {
        if ($value === null) {
            unset($this->data[$name]);

            return;
        }

        $this->data[$name] = $value;
    }
}
