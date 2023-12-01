<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class ReadOnlyRepository implements RepositoryInterface
{
    /** @var RepositoryInterface */
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $name): ?string
    {
        return $this->repository->get($name);
    }

    public function set(string $name, ?string $value): void
    {
    }
}
