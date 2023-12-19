<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Exception\Repository;

use InvalidArgumentException;
use Throwable;

use function sprintf;

class InvalidName extends InvalidArgumentException implements RepositoryException
{
    protected string|null $name = null;

    public static function fromName(
        string $name,
        int $code = 0,
        Throwable|null $previous = null,
    ): self {
        $message = sprintf(
            'Invalid environment variable name "%s"',
            $name,
        );

        $exception       = new self($message, $code, $previous);
        $exception->name = $name;

        return $exception;
    }

    public function getName(): string|null
    {
        return $this->name;
    }
}
