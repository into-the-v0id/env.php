<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class ArrayRepositoryTest extends TestCase
{
    private static function createRepository(): Repository\ArrayRepository
    {
        return new Repository\ArrayRepository([
            'GET_EMPTY' => '',
            'GET_STRING' => 'Hello World',
            'SET_OVERWRITE' => 'Hello World',
            'SET_NULL' => 'Hello World',
        ]);
    }

    /**
     * @return array<array<mixed>>
     */
    public function getterProvider(): array
    {
        return [
            ['GET_UNSET', null],
            ['GET_EMPTY', ''],
            ['GET_STRING', 'Hello World'],
        ];
    }

    /**
     * @dataProvider getterProvider
     */
    public function testGet(string $name, ?string $result): void
    {
        $this->assertEquals(
            $result,
            self::createRepository()->get($name)
        );
    }

    /**
     * @return array<array<mixed>>
     */
    public function setterProvider(): array
    {
        return [
            ['SET_EMPTY', ''],
            ['SET_STRING', 'Hello World'],
            ['SET_OVERWRITE', 'Hello World v2'],
            ['SET_NULL', null],
        ];
    }

    /**
     * @dataProvider setterProvider
     */
    public function testSet(string $name, ?string $value): void
    {
        $repository = self::createRepository();
        $repository->set($name, $value);

        $this->assertEquals(
            $value,
            $repository->get($name)
        );
    }
}
