<?php

declare(strict_types=1);

namespace IntoTheVoid\Env;

use IntoTheVoid\Env\Exception\MissingEnvironmentVariable;
use IntoTheVoid\Env\Exception\Parser\ParserException;
use IntoTheVoid\Env\Exception\Parser\UnparsableEnvironmentVariable;
use IntoTheVoid\Env\Exception\Parser\UnparsableValue;
use IntoTheVoid\Env\Helper\Str;
use IntoTheVoid\Env\Normalizer\NormalizerInterface;
use IntoTheVoid\Env\Repository\RepositoryChain;
use IntoTheVoid\Env\Repository\RepositoryInterface;

use function array_map;
use function in_array;
use function preg_match;
use function strtolower;

// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations

/**
 * @copyright Oliver Amann
 * @license MIT
 */
class Env
{
    protected static RepositoryInterface|null $repository = null;

    protected static NormalizerInterface|null $normalizer = null;

    private function __construct() // Prevent instantiation
    {
    }

    public static function getRepository(): RepositoryInterface
    {
        if (static::$repository === null) {
            static::$repository = new RepositoryChain([
                new Repository\ReadOnlyRepository(new Repository\GetenvRepository(true)),
                new Repository\GetenvRepository(),
                new Repository\WriteOnlyRepository(new Repository\EnvArrayRepository()),
                new Repository\WriteOnlyRepository(new Repository\ServerArrayRepository()),
            ]);
        }

        return static::$repository;
    }

    public static function setRepository(RepositoryInterface $repository): void
    {
        static::$repository = $repository;
    }

    public static function getNormalizer(): NormalizerInterface
    {
        if (static::$normalizer === null) {
            static::$normalizer = new Normalizer\NoopNormalizer();
        }

        return static::$normalizer;
    }

    public static function setNormalizer(NormalizerInterface $normalizer): void
    {
        static::$normalizer = $normalizer;
    }

    public static function parseString(string $value, bool $normalize = true): string|null
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        $lowercaseValue = strtolower($value);
        if (in_array($lowercaseValue, ['', 'null', 'nil', 'none', 'undefined', 'empty'], true)) {
            return null;
        }

        return $value;
    }

    public static function parseBool(string $value, bool $normalize = true): bool|null
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        $lowercaseValue = strtolower($value);

        if (in_array($lowercaseValue, ['1', 'true', 'y', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array($lowercaseValue, ['0', 'false', 'n', 'no', 'off'], true)) {
            return false;
        }

        return null;
    }

    public static function parseInt(string $value, bool $normalize = true): int|null
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        $isInt = preg_match('/^[+\\-]?(0|[1-9][0-9]*)$/', $value) === 1;
        if (! $isInt) {
            return null;
        }

        return (int) $value;
    }

    public static function parseFloat(string $value, bool $normalize = true): float|null
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        $isFloat = preg_match('/^[+\\-]?(0|[1-9][0-9]*)(?:\\.[0-9]+)?$/', $value) === 1;
        if (! $isFloat) {
            return null;
        }

        return (float) $value;
    }

    /** @return (int|float|bool|string|null)[] */
    public static function parseList(string $value, string $separator, bool $normalize = true): array
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        if ($value === '') {
            return [];
        }

        $items = Str::split($value, $separator);

        return array_map(
            static fn (string $item) => static::parse($item),
            $items,
        );
    }

    public static function parse(string $value, bool $normalize = true): int|float|bool|string|null
    {
        if ($normalize) {
            $value = static::getNormalizer()->normalize($value);
        }

        $stringValue = static::parseString($value, false);
        if ($stringValue === null) {
            return null;
        }

        return static::parseInt($value, false)
            ?? static::parseFloat($value, false)
            ?? static::parseBool($value, false)
            ?? $stringValue;
    }

    /** @throws UnparsableValue */
    public static function parseStrictBool(string $value, bool $normalize = true): bool
    {
        return static::parseBool($value, $normalize)
            ?? throw UnparsableValue::create($value, 'bool');
    }

    /** @throws UnparsableValue */
    public static function parseStrictInt(string $value, bool $normalize = true): int
    {
        return static::parseInt($value, $normalize)
            ?? throw UnparsableValue::create($value, 'int');
    }

    /** @throws UnparsableValue */
    public static function parseStrictFloat(string $value, bool $normalize = true): float
    {
        return static::parseFloat($value, $normalize)
            ?? throw UnparsableValue::create($value, 'float');
    }

    public static function getRaw(string $name): string|null
    {
        return static::getRepository()->get($name);
    }

    public static function getString(string $name): string|null
    {
        $value = static::getRaw($name);
        if ($value === null) {
            return null;
        }

        return static::parseString($value);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getBool(string $name, bool $strict = true): bool|null
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictBool($value, false);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'bool', 0, $e);
            }
        }

        return static::parseBool($value, false);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getInt(string $name, bool $strict = true): int|null
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictInt($value, false);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'int', 0, $e);
            }
        }

        return static::parseInt($value, false);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getFloat(string $name, bool $strict = true): float|null
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictFloat($value, false);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'float', 0, $e);
            }
        }

        return static::parseFloat($value, false);
    }

    /** @return (int|float|bool|string|null)[]|null */
    public static function getList(string $name, string $separator): array|null
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        return static::parseList($value, $separator, false);
    }

    public static function get(string $name): int|float|bool|string|null
    {
        $value = static::getRaw($name);
        if ($value === null) {
            return null;
        }

        return static::parse($value);
    }

    /** @throws MissingEnvironmentVariable */
    public static function getRequiredRaw(string $name): string
    {
        return static::getRaw($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /** @throws MissingEnvironmentVariable */
    public static function getRequiredString(string $name): string
    {
        return static::getString($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredBool(string $name): bool
    {
        return static::getBool($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredInt(string $name): int
    {
        return static::getInt($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredFloat(string $name): float
    {
        return static::getFloat($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /**
     * @return (int|float|bool|string|null)[]
     *
     * @throws MissingEnvironmentVariable
     */
    public static function getRequiredList(string $name, string $separator): array
    {
        return static::getList($name, $separator)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /** @throws MissingEnvironmentVariable */
    public static function getRequired(string $name): int|float|bool|string
    {
        return static::get($name)
            ?? throw MissingEnvironmentVariable::fromName($name);
    }

    /**
     * Checks if the environment variable exists and has an actual value
     */
    public static function has(string $name): bool
    {
        return static::getString($name) !== null;
    }

    /**
     * Checks if the environment variable exists
     */
    public static function exists(string $name): bool
    {
        return static::getRaw($name) !== null;
    }

    public static function set(string $name, mixed $value): void
    {
        static::getRepository()->set($name, Str::from($value));
    }

    public static function remove(string $name): void
    {
        static::getRepository()->set($name, null);
    }
}
