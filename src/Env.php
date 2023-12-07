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
    /** @var RepositoryInterface|null */
    protected static $repository = null;

    /** @var NormalizerInterface|null */
    protected static $normalizer = null;

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

    public static function parseString(string $value): ?string
    {
        $value = static::getNormalizer()->normalize($value);

        $lowercaseValue = strtolower($value);
        if (in_array($lowercaseValue, ['', 'null', 'nil', 'none', 'undefined', 'empty'], true)) {
            return null;
        }

        return $value;
    }

    public static function parseBool(string $value): ?bool
    {
        $value = static::getNormalizer()->normalize($value);

        $lowercaseValue = strtolower($value);

        if (in_array($lowercaseValue, ['1', 'true', 'y', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array($lowercaseValue, ['0', 'false', 'n', 'no', 'off'], true)) {
            return false;
        }

        return null;
    }

    public static function parseInt(string $value): ?int
    {
        $value = static::getNormalizer()->normalize($value);

        $isInt = preg_match('/^[+\\-]?(0|[1-9][0-9]*)$/', $value) === 1;
        if (! $isInt) {
            return null;
        }

        return (int) $value;
    }

    public static function parseFloat(string $value): ?float
    {
        $value = static::getNormalizer()->normalize($value);

        $isFloat = preg_match('/^[+\\-]?(0|[1-9][0-9]*)(?:\\.[0-9]+)?$/', $value) === 1;
        if (! $isFloat) {
            return null;
        }

        return (float) $value;
    }

    /** @return (int|float|bool|string|null)[] */
    public static function parseList(string $value, string $separator): array
    {
        $value = static::getNormalizer()->normalize($value);

        if ($value === '') {
            return [];
        }

        $items = Str::split($value, $separator);

        return array_map(
            static function (string $item) {
                return static::parse($item);
            },
            $items
        );
    }

    /** @return int|float|bool|string|null */
    public static function parse(string $value)
    {
        return static::parseInt($value)
            ?? static::parseFloat($value)
            ?? static::parseBool($value)
            ?? static::parseString($value);
    }

    /** @throws UnparsableValue */
    public static function parseStrictBool(string $rawValue): bool
    {
        $parsedValue = static::parseBool($rawValue);
        if ($parsedValue === null) {
            throw UnparsableValue::create($rawValue, 'bool');
        }

        return $parsedValue;
    }

    /** @throws UnparsableValue */
    public static function parseStrictInt(string $rawValue): int
    {
        $parsedValue = static::parseInt($rawValue);
        if ($parsedValue === null) {
            throw UnparsableValue::create($rawValue, 'int');
        }

        return $parsedValue;
    }

    /** @throws UnparsableValue */
    public static function parseStrictFloat(string $rawValue): float
    {
        $parsedValue = static::parseFloat($rawValue);
        if ($parsedValue === null) {
            throw UnparsableValue::create($rawValue, 'float');
        }

        return $parsedValue;
    }

    public static function getRaw(string $name): ?string
    {
        return static::getRepository()->get($name);
    }

    public static function getString(string $name): ?string
    {
        $value = static::getRaw($name);
        if ($value === null) {
            return null;
        }

        return static::parseString($value);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getBool(string $name, bool $strict = true): ?bool
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictBool($value);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'bool', 0, $e);
            }
        }

        return static::parseBool($value);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getInt(string $name, bool $strict = true): ?int
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictInt($value);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'int', 0, $e);
            }
        }

        return static::parseInt($value);
    }

    /** @throws UnparsableEnvironmentVariable */
    public static function getFloat(string $name, bool $strict = true): ?float
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        if ($strict) {
            try {
                return static::parseStrictFloat($value);
            } catch (ParserException $e) {
                throw UnparsableEnvironmentVariable::create($name, $value, 'float', 0, $e);
            }
        }

        return static::parseFloat($value);
    }

    /** @return (int|float|bool|string|null)[]|null */
    public static function getList(string $name, string $separator): ?array
    {
        $value = static::getString($name);
        if ($value === null) {
            return null;
        }

        return static::parseList($value, $separator);
    }

    /** @return int|float|bool|string|null */
    public static function get(string $name)
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
        $value = static::getRaw($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /** @throws MissingEnvironmentVariable */
    public static function getRequiredString(string $name): string
    {
        $value = static::getString($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredBool(string $name): bool
    {
        $value = static::getBool($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredInt(string $name): int
    {
        $value = static::getInt($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * @throws MissingEnvironmentVariable
     * @throws UnparsableEnvironmentVariable
     */
    public static function getRequiredFloat(string $name): float
    {
        $value = static::getFloat($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * @return (int|float|bool|string|null)[]
     *
     * @throws MissingEnvironmentVariable
     */
    public static function getRequiredList(string $name, string $separator): array
    {
        $value = static::getList($name, $separator);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * @return int|float|bool|string
     *
     * @throws MissingEnvironmentVariable
     */
    public static function getRequired(string $name)
    {
        $value = static::get($name);
        if ($value === null) {
            throw MissingEnvironmentVariable::fromName($name);
        }

        return $value;
    }

    /**
     * Check if the environment variable exists and has an actual value
     */
    public static function has(string $name): bool
    {
        return static::getString($name) !== null;
    }

    /**
     * Check if the environment variable exists
     */
    public static function exists(string $name): bool
    {
        return static::getRaw($name) !== null;
    }

    /** @param mixed $value */
    public static function set(string $name, $value): void
    {
        static::getRepository()->set($name, Str::from($value));
    }

    public static function remove(string $name): void
    {
        static::getRepository()->set($name, null);
    }
}
