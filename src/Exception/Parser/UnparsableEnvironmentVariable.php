<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Exception\Parser;

use Throwable;
use UnexpectedValueException;

use function sprintf;

class UnparsableEnvironmentVariable extends UnexpectedValueException implements ParserException
{
    /** @var string|null */
    protected $name = null;

    /** @var string|null */
    protected $value = null;

    public static function create(
        string $name,
        ?string $value = null,
        ?string $targetType = null,
        int $code = 0,
        ?Throwable $previous = null
    ): self {
        $message = sprintf('Unable to parse environment variable "%s"', $name);

        if ($value !== null) {
            $message .= sprintf(' with value "%s"', $value);
        }

        if ($targetType !== null) {
            $message .= sprintf(' as type %s', $targetType);
        }

        $exception       = new self($message, $code, $previous);
        $exception->name = $name;

        return $exception;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
