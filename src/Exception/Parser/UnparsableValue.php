<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Exception\Parser;

use Throwable;
use UnexpectedValueException;

use function sprintf;

class UnparsableValue extends UnexpectedValueException implements ParserException
{
    protected string|null $value = null;

    public static function create(
        string $value,
        string|null $targetType = null,
        int $code = 0,
        Throwable|null $previous = null,
    ): self {
        $message = sprintf('Unable to parse value "%s"', $value);

        if ($targetType !== null) {
            $message .= sprintf(' as type %s', $targetType);
        }

        $exception        = new self($message, $code, $previous);
        $exception->value = $value;

        return $exception;
    }

    public function getValue(): string|null
    {
        return $this->value;
    }
}
