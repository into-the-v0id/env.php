<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class WriteOnlyRepository implements RepositoryInterface
{
    public function __construct(
        protected RepositoryInterface $repository,
    ) {
    }

    public function get(string $name): string|null
    {
        return null;
    }

    public function set(string $name, string|null $value): void
    {
        $this->repository->set($name, $value);
    }
}
