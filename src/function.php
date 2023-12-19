<?php

declare(strict_types=1);

namespace IntoTheVoid\Env;

// phpcs:disable Squiz.Functions.GlobalFunction.Found

function env(string $name): int|float|bool|string|null
{
    return Env::get($name);
}
