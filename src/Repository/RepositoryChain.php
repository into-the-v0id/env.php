<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class RepositoryChain implements RepositoryInterface
{
    /** @param RepositoryInterface[] $repositories */
    public function __construct(
        protected array $repositories,
    ) {
    }

    public function get(string $name): string|null
    {
        foreach ($this->repositories as $repository) {
            $value = $repository->get($name);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    public function set(string $name, string|null $value): void
    {
        foreach ($this->repositories as $repository) {
            $repository->set($name, $value);
        }
    }
}
