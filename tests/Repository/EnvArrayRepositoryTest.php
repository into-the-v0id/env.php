<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class EnvArrayRepositoryTest extends TestCase
{
    private static function createRepository(): Repository\EnvArrayRepository
    {
        return new Repository\EnvArrayRepository();
    }

    protected function setUp(): void
    {
        unset($_ENV['GET_UNSET']);
        $_ENV['GET_EMPTY']  = '';
        $_ENV['GET_STRING'] = 'Hello World';
        unset($_ENV['SET_EMPTY']);
        unset($_ENV['SET_STRING']);
        $_ENV['SET_OVERWRITE'] = 'Hello World';
        $_ENV['SET_NULL']      = 'Hello World';
    }

    /** @return array<array<mixed>> */
    public function getterProvider(): array
    {
        return [
            ['GET_UNSET', null],
            ['GET_EMPTY', ''],
            ['GET_STRING', 'Hello World'],
        ];
    }

    /** @dataProvider getterProvider */
    public function testGet(string $name, string|null $result): void
    {
        $this->assertEquals(
            $result,
            self::createRepository()->get($name),
        );
    }

    /** @return array<array<mixed>> */
    public function setterProvider(): array
    {
        return [
            ['SET_EMPTY', ''],
            ['SET_STRING', 'Hello World'],
            ['SET_OVERWRITE', 'Hello World v2'],
            ['SET_NULL', null],
        ];
    }

    /** @dataProvider setterProvider */
    public function testSet(string $name, string|null $value): void
    {
        self::createRepository()->set($name, $value);

        $this->assertEquals(
            $value,
            $_ENV[$name] ?? null,
        );
    }
}
