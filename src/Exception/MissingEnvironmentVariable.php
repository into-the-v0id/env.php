<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Exception;

use Throwable;
use UnexpectedValueException;

use function sprintf;

class MissingEnvironmentVariable extends UnexpectedValueException implements Exception
{
    /** @var string|null */
    protected $name = null;

    public static function fromName(
        string $name,
        int $code = 0,
        ?Throwable $previous = null
    ): self {
        $message = sprintf(
            'Missing environment variable "%s"',
            $name
        );

        $exception       = new self($message, $code, $previous);
        $exception->name = $name;

        return $exception;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
