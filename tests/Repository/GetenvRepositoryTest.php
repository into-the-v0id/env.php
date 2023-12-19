<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

use function getenv;
use function putenv;

final class GetenvRepositoryTest extends TestCase
{
    private static function createRepository(): Repository\GetenvRepository
    {
        return new Repository\GetenvRepository();
    }

    protected function setUp(): void
    {
        putenv('GET_UNSET');
        putenv('GET_EMPTY=');
        putenv('GET_STRING=Hello World');
        putenv('SET_EMPTY');
        putenv('SET_STRING');
        putenv('SET_OVERWRITE=Hello World');
        putenv('SET_NULL=Hello World');
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
            ['SET_EMPTY', '', ''],
            ['SET_STRING', 'Hello World', 'Hello World'],
            ['SET_OVERWRITE', 'Hello World v2', 'Hello World v2'],
            ['SET_NULL', null, false],
        ];
    }

    /** @dataProvider setterProvider */
    public function testSet(string $name, string|null $value, string|false $result): void
    {
        self::createRepository()->set($name, $value);

        $this->assertEquals(
            $result,
            getenv($name),
        );
    }
}
