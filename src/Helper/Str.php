<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Helper;

use function explode;
use function is_float;
use function is_string;
use function mb_str_split;
use function mb_strlen;
use function mb_substr;
use function str_contains;
use function str_ends_with;
use function str_starts_with;

/** @internal */
class Str
{
    private function __construct() // Prevent instantiation
    {
    }

    public static function unwrap(string $value, string $left, string $right): string
    {
        if (! str_starts_with($value, $left) || ! str_ends_with($value, $right)) {
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

    /** @return string[] */
    public static function split(string $value, string $separator): array
    {
        if ($value === '') {
            return [''];
        }

        if ($separator === '') {
            return mb_str_split($value);
        }

        return explode($separator, $value);
    }

    public static function from(mixed $value): string
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
            $value = (string) $value;
            if (! str_contains($value, '.')) {
                $value .= '.0';
            }

            return $value;
        }

        return (string) $value;
    }
}
