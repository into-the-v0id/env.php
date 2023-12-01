<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Repository;

use IntoTheVoid\Env\Exception\Repository\FailedWrite;
use IntoTheVoid\Env\Exception\Repository\InvalidName;

use function getenv;
use function putenv;

class GetenvRepository implements RepositoryInterface
{
    /** @var bool */
    protected $localOnly;

    public function __construct(bool $localOnly = false)
    {
        $this->localOnly = $localOnly;
    }

    public function get(string $name): ?string
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        $value = getenv($name, $this->localOnly);
        if ($value === false) {
            return null;
        }

        return $value;
    }

    public function set(string $name, ?string $value): void
    {
        if ($name === '') {
            throw InvalidName::fromName($name);
        }

        if ($value === null) {
            $success = putenv($name);
            if (! $success) {
                throw FailedWrite::fromName($name);
            }

            return;
        }

        $success = putenv($name . '=' . $value);
        if (! $success) {
            throw FailedWrite::fromName($name);
        }
    }
}
