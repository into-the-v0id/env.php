<?php

declare(strict_types=1);

namespace IntoTheVoid\Env;

// phpcs:disable Squiz.Functions.GlobalFunction.Found

/**
 * @return int|float|bool|string|null
 */
function env(string $name)
{
    return Env::get($name);
}
