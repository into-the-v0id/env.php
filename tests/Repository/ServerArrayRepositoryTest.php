<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class ServerArrayRepositoryTest extends TestCase
{
    private static function createRepository(): Repository\ServerArrayRepository
    {
        return new Repository\ServerArrayRepository();
    }

    protected function setUp(): void
    {
        unset($_SERVER['GET_UNSET']);
        $_SERVER['GET_EMPTY']  = '';
        $_SERVER['GET_STRING'] = 'Hello World';
        unset($_SERVER['SET_EMPTY']);
        unset($_SERVER['SET_STRING']);
        $_SERVER['SET_OVERWRITE'] = 'Hello World';
        $_SERVER['SET_NULL']      = 'Hello World';
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
            $_SERVER[$name] ?? null,
        );
    }
}
