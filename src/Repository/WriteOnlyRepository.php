<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class WriteOnlyRepository implements RepositoryInterface
{
    /** @var RepositoryInterface */
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $name): ?string
    {
        return null;
    }

    public function set(string $name, ?string $value): void
    {
        $this->repository->set($name, $value);
    }
}
