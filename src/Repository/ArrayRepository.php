<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

use IntoTheVoid\Env\Helper\Str;

class ArrayRepository implements RepositoryInterface
{
    /** @var mixed[] */
    protected $data;

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get(string $name): ?string
    {
        if (! isset($this->data[$name])) {
            return null;
        }

        return Str::from($this->data[$name]);
    }

    public function set(string $name, ?string $value): void
    {
        if ($value === null) {
            unset($this->data[$name]);

            return;
        }

        $this->data[$name] = $value;
    }
}
