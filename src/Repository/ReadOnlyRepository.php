<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class ReadOnlyRepository implements RepositoryInterface
{
    public function __construct(
        protected RepositoryInterface $repository,
    ) {
    }

    public function get(string $name): string|null
    {
        return $this->repository->get($name);
    }

    public function set(string $name, string|null $value): void
    {
    }
}
