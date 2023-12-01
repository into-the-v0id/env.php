# Env

![Latest Packagist Version](https://img.shields.io/packagist/v/into-the-void/env)
![Latest Packagist License](https://img.shields.io/packagist/l/into-the-void/env)

Read, parse and modify environment variables

## Installation

You can install this library via [composer](https://getcomposer.org/):

```bash
composer require into-the-void/env
```

## Example

```php
use IntoTheVoid\Env\Env;

getenv('APP_DEBUG');   // string(4) "true"
Env::get('APP_DEBUG'); // bool(true)

Env::getString('DB_HOST');          // string(9) "localhost"
Env::getBool('APP_DEBUG');          // bool(true)
Env::getInt('DB_PORT');             // int(5432)
Env::getFloat('APP_MUL');           // double(1.25)
Env::getList('PATH', ':');          // array(2) { "/usr/local/bin", "/usr/bin" }
Env::getRequiredInt('NO_SUCH_VAR'); // Fatal error: MissingEnvironmentVariable
Env::getRaw('APP_DEBUG');           // string(4) "true"

Env::has('DB_PORT');          // bool(true)
Env::set('APP_DEBUG', false);
Env::remove('APP_DEBUG');
```

You may also use the helper function instead of calling `Env::get()`:
```php
use function IntoTheVoid\Env\env;

env('APP_DEBUG'); // bool(true)
```

## Behaviour

### Repository

By default, environment variables are read via `getenv(local_only: true)` and `getenv()`. They are written via `putenv()`, `$_ENV` and `$_SERVER`.

You may change this behaviour using `Env::setRepository()`. Have a look at [src/Repository](./src/Repository) for available Repositories.

### Normalizer

If you want to strip spaces or quotes from your environment variables, you may configure this via `Env::setNormalizer()`. Have a look at [src/Normalizer](./src/Normalizer) for available Normalizers.

### Parser

**Nullish values** (case insensitive): `''`, `'null'`, `'nil'`, `'none'`, `'undefined'`, `'empty'`  
**Truthy values** (case insensitive): `'1'`, `'true'`, `'y'`, `'yes'`, `'on'`  
**Falsy values** (case insensitive): `'0'`, `'false'`, `'n'`, `'no'`, `'off'`

## License

Copyright (C) Oliver Amann

This project is licensed under the MIT License (MIT). Please see [LICENSE](./LICENSE) for more information.
