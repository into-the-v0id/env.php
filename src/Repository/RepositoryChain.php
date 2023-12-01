<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

class RepositoryChain implements RepositoryInterface
{
    /** @var RepositoryInterface[] */
    protected $repositories;

    /**
     * @param RepositoryInterface[] $repositories
     */
    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    public function get(string $name): ?string
    {
        foreach ($this->repositories as $repository) {
            $value = $repository->get($name);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    public function set(string $name, ?string $value): void
    {
        foreach ($this->repositories as $repository) {
            $repository->set($name, $value);
        }
    }
}
