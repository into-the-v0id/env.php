<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Helper;

use function explode;
use function ini_get;
use function is_float;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function preg_match;
use function sprintf;
use function substr;

/**
 * @internal
 */
class Str
{
    private function __construct() // Prevent instantiation
    {
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return mb_strpos($haystack, $needle) === 0;
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return mb_substr($haystack, -mb_strlen($needle)) === $needle;
    }

    public static function contains(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return mb_strpos($haystack, $needle) !== false;
    }

    public static function unwrap(string $value, string $left, string $right): string
    {
        if (! self::startsWith($value, $left) || ! self::endsWith($value, $right)) {
            return $value;
        }

        // Ensure $left and $right dont overlap
        if (mb_strlen($value) < mb_strlen($left) + mb_strlen($right)) {
            return $value;
        }

        if ($left !== '') {
            $value = mb_substr($value, mb_strlen($left));
        }

        if ($right !== '') {
            $value = mb_substr($value, 0, -mb_strlen($right));
        }

        return $value;
    }

    /**
     * @return string[]
     */
    public static function split(string $value, string $separator): array
    {
        if ($value === '') {
            return [''];
        }

        if ($separator === '') {
            $values = [];

            $length = mb_strlen($value);
            for ($i = 0; $i < $length; $i++) {
                $values[] = mb_substr($value, $i, 1);
            }

            return $values;
        }

        return explode($separator, $value);
    }

    /**
     * @param mixed $value
     */
    public static function from($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_float($value)) {
            $precision = ini_get('precision');
            if ($precision === false || $precision === '' || preg_match('/^[0-9]+$/', $precision) !== 1) {
                $precision = '14';
            }

            $value = sprintf('%.' . $precision . 'F', $value);
            while (self::endsWith($value, '0') && ! self::endsWith($value, '.0')) {
                $value = substr($value, 0, -1);
            }

            if (! self::contains($value, '.')) {
                $value .= '.0';
            }

            return $value;
        }

        return (string) $value;
    }
}
