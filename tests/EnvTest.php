<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test;

use IntoTheVoid\Env\Env;
use IntoTheVoid\Env\Exception\MissingEnvironmentVariable;
use IntoTheVoid\Env\Exception\Parser\UnparsableEnvironmentVariable;
use IntoTheVoid\Env\Exception\Parser\UnparsableValue;
use IntoTheVoid\Env\Normalizer;
use IntoTheVoid\Env\Repository;

final class EnvTest extends TestCase
{
    /** @param mixed[]|null $data */
    private function configureRepository(array|null $data): void
    {
        if ($data === null) {
            $repository = new Repository\ArrayRepository([]);
        } else {
            $repository = $this->createMock(Repository\RepositoryInterface::class);

            foreach ($data as $param => $return) {
                $repository->method('get')
                    ->with($param)
                    ->willReturn($return);
            }
        }

        Env::setRepository($repository);
    }

    /** @param mixed[]|null $data */
    private function configureNormalizer(array|null $data): void
    {
        if ($data === null) {
            $normalizer = new Normalizer\NoopNormalizer();
        } else {
            $normalizer = $this->createMock(Normalizer\NormalizerInterface::class);

            foreach ($data as $param => $return) {
                $normalizer->method('normalize')
                    ->with($param)
                    ->willReturn($return);
            }
        }

        Env::setNormalizer($normalizer);
    }

    public function testGetDefaultRepository(): void
    {
        Env::getRepository();
    }

    public function testSetRepository(): void
    {
        $repository = new Repository\ArrayRepository([]);

        Env::setRepository($repository);

        $this->assertSame($repository, Env::getRepository());
    }

    public function testGetDefaultNormalizer(): void
    {
        Env::getNormalizer();
    }

    public function testSetNormalizer(): void
    {
        $normalizer = new Normalizer\NoopNormalizer();

        Env::setNormalizer($normalizer);

        $this->assertSame($normalizer, Env::getNormalizer());
    }

    /** @return array<array<mixed>> */
    public function parseProvider(): array
    {
        return [
            ['parseString', [''], null, null, null],
            ['parseString', ['null'], null, null, null],
            ['parseString', ['Hello World'], 'Hello World', null, null],
            ['parseBool', [''], null, null, null],
            ['parseBool', ['null'], null, null, null],
            ['parseBool', ['1'], true, null, null],
            ['parseBool', ['true'], true, null, null],
            ['parseBool', ['0'], false, null, null],
            ['parseBool', ['false'], false, null, null],
            ['parseBool', ['not a bool'], null, null, null],
            ['parseInt', [''], null, null, null],
            ['parseInt', ['null'], null, null, null],
            ['parseInt', ['0'], 0, null, null],
            ['parseInt', ['1'], 1, null, null],
            ['parseInt', ['+12345'], 12345, null, null],
            ['parseInt', ['012345'], null, null, null],
            ['parseInt', ['+012345'], null, null, null],
            ['parseInt', ['-3'], -3, null, null],
            ['parseInt', ['0.5'], null, null, null],
            ['parseInt', ['not an int'], null, null, null],
            ['parseFloat', [''], null, null, null],
            ['parseFloat', ['null'], null, null, null],
            ['parseFloat', ['0.0'], 0.0, null, null],
            ['parseFloat', ['1.0'], 1.0, null, null],
            ['parseFloat', ['01.0'], null, null, null],
            ['parseFloat', ['+0.01'], 0.01, null, null],
            ['parseFloat', ['-3.5'], -3.5, null, null],
            ['parseFloat', ['1'], 1.0, null, null],
            ['parseFloat', ['+123'], 123.0, null, null],
            ['parseFloat', ['-123'], -123.0, null, null],
            ['parseFloat', ['0123'], null, null, null],
            ['parseFloat', ['.5'], null, null, null],
            ['parseFloat', ['not a float'], null, null, null],
            ['parseList', ['', ';'], [], null, null],
            ['parseList', ['a;b;c', ';'], ['a', 'b', 'c'], null, null],
            ['parseList', [',1,3.5,true', ','], ['', 1, 3.5, true], null, null],
            ['parseList', ['a', '|'], ['a'], null, null],
            ['parse', [''], null, null, null],
            ['parse', ['null'], null, null, null],
            ['parse', ['true'], true, null, null],
            ['parse', ['false'], false, null, null],
            ['parse', ['1'], 1, null, null],
            ['parse', ['-3'], -3, null, null],
            ['parse', ['+0.01'], 0.01, null, null],
            ['parse', ['-3.5'], -3.5, null, null],
            ['parse', ['hello world'], 'hello world', null, null],
            ['parseStrictBool', ['true'], true, null, null],
            ['parseStrictBool', ['false'], false, null, null],
            ['parseStrictBool', ['not a bool'], null, UnparsableValue::class, null],
            ['parseStrictInt', ['1'], 1, null, null],
            ['parseStrictInt', ['-3'], -3, null, null],
            ['parseStrictInt', ['not an int'], null, UnparsableValue::class, null],
            ['parseStrictFloat', ['1.0'], 1.0, null, null],
            ['parseStrictFloat', ['-3.5'], -3.5, null, null],
            ['parseStrictFloat', ['not a float'], null, UnparsableValue::class, null],
        ];
    }

    /**
     * @param mixed[]      $arguments
     * @param mixed[]|null $normalizerData
     *
     * @dataProvider parseProvider
     */
    public function testParse(
        string $methodName,
        array $arguments,
        mixed $result,
        string|null $expectedException,
        array|null $normalizerData,
    ): void {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->configureRepository([]);
        $this->configureNormalizer($normalizerData);

        $this->assertEquals(
            $result,
            Env::{$methodName}(...$arguments),
        );
    }

    /** @return array<array<mixed>> */
    public function getterProvider(): array
    {
        return [
            ['getRaw', ['GET'], 'Raw Value', null, ['GET' => 'Raw Value'], null],
            ['getRaw', ['GET'], null, null, ['GET' => null], null],
            ['getString', ['GET'], 'Hello World', null, ['GET' => 'Hello World'], null],
            ['getString', ['GET'], null, null, ['GET' => 'null'], null],
            ['getString', ['GET'], null, null, ['GET' => null], null],
            ['getBool', ['GET'], true, null, ['GET' => 'true'], null],
            ['getBool', ['GET'], null, null, ['GET' => ''], null],
            ['getBool', ['GET'], null, null, ['GET' => null], null],
            ['getBool', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getBool', ['GET', false], null, null, ['GET' => 'invalid'], null],
            ['getInt', ['GET'], -3, null, ['GET' => '-3'], null],
            ['getInt', ['GET'], null, null, ['GET' => ''], null],
            ['getInt', ['GET'], null, null, ['GET' => null], null],
            ['getInt', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getInt', ['GET', false], null, null, ['GET' => 'invalid'], null],
            ['getFloat', ['GET'], 1.25, null, ['GET' => '1.25'], null],
            ['getFloat', ['GET'], -3.0, null, ['GET' => '-3'], null],
            ['getFloat', ['GET'], null, null, ['GET' => ''], null],
            ['getFloat', ['GET'], null, null, ['GET' => null], null],
            ['getFloat', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getFloat', ['GET', false], null, null, ['GET' => 'invalid'], null],
            ['getList', ['GET', ';'], [1, 'Hello', true], null, ['GET' => '1;Hello;true'], null],
            ['getList', ['GET', ''], ['h', 'e', 'l', 'l', 'o'], null, ['GET' => 'hello'], null],
            ['getList', ['GET', ','], null, null, ['GET' => ''], null],
            ['getList', ['GET', '|'], null, null, ['GET' => 'null'], null],
            ['getList', ['GET', ' '], null, null, ['GET' => null], null],
            ['get', ['GET'], 'Hello World', null, ['GET' => 'Hello World'], null],
            ['get', ['GET'], false, null, ['GET' => 'n'], null],
            ['get', ['GET'], 3, null, ['GET' => '3'], null],
            ['get', ['GET'], -1.5, null, ['GET' => '-1.50'], null],
            ['get', ['GET'], null, null, ['GET' => ''], null],
            ['get', ['GET'], null, null, ['GET' => null], null],
            ['getRequiredRaw', ['GET'], '1.250', null, ['GET' => '1.250'], null],
            ['getRequiredRaw', ['GET'], 'null', null, ['GET' => 'null'], null],
            ['getRequiredRaw', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredString', ['GET'], 'Hello World', null, ['GET' => 'Hello World'], null],
            ['getRequiredString', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredString', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => 'null'], null],
            ['getRequiredBool', ['GET'], false, null, ['GET' => '0'], null],
            ['getRequiredBool', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredBool', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => 'null'], null],
            ['getRequiredBool', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getRequiredInt', ['GET'], '1', null, ['GET' => '1'], null],
            ['getRequiredInt', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredInt', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => 'null'], null],
            ['getRequiredInt', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getRequiredFloat', ['GET'], -25.0, null, ['GET' => '-25'], null],
            ['getRequiredFloat', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredFloat', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => 'null'], null],
            ['getRequiredFloat', ['GET'], null, UnparsableEnvironmentVariable::class, ['GET' => 'invalid'], null],
            ['getRequiredList', ['GET', ','], ['a', 'b', 'c'], null, ['GET' => 'a,b,c'], null],
            ['getRequiredList', ['GET', ','], null, MissingEnvironmentVariable::class, ['GET' => null], null],
            ['getRequiredList', ['GET', ','], null, MissingEnvironmentVariable::class, ['GET' => 'null'], null],
            ['getRequired', ['GET'], true, null, ['GET' => 'y'], null],
            ['getRequired', ['GET'], 1, null, ['GET' => '1'], null],
            ['getRequired', ['GET'], -1.25, null, ['GET' => '-1.25'], null],
            ['getRequired', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => 'empty'], null],
            ['getRequired', ['GET'], 'Hello World', null, ['GET' => 'Hello World'], null],
            ['getRequired', ['GET'], null, MissingEnvironmentVariable::class, ['GET' => null], null],
        ];
    }

    /**
     * @param mixed[]      $arguments
     * @param mixed[]|null $repositoryData
     * @param mixed[]|null $normalizerData
     *
     * @dataProvider getterProvider
     */
    public function testGet(
        string $methodName,
        array $arguments,
        mixed $result,
        string|null $expectedException,
        array|null $repositoryData,
        array|null $normalizerData,
    ): void {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->configureRepository($repositoryData);
        $this->configureNormalizer($normalizerData);

        $this->assertEquals(
            $result,
            Env::{$methodName}(...$arguments),
        );
    }

    /** @return array<array<mixed>> */
    public function hasProvider(): array
    {
        return [
            ['HAS', true, ['HAS' => 'Raw Value'], null],
            ['HAS', false, ['HAS' => ''], null],
            ['HAS', false, ['HAS' => 'null'], null],
            ['HAS', false, ['HAS' => null], null],
        ];
    }

    /**
     * @param mixed[]|null $repositoryData
     * @param mixed[]|null $normalizerData
     *
     * @dataProvider hasProvider
     */
    public function testHas(
        string $name,
        bool $result,
        array|null $repositoryData,
        array|null $normalizerData,
    ): void {
        $this->configureRepository($repositoryData);
        $this->configureNormalizer($normalizerData);

        $this->assertEquals(
            $result,
            Env::has($name),
        );
    }

    /** @return array<array<mixed>> */
    public function existsProvider(): array
    {
        return [
            ['EXISTS', true, ['EXISTS' => 'Raw Value']],
            ['EXISTS', true, ['EXISTS' => '']],
            ['EXISTS', true, ['EXISTS' => 'null']],
            ['EXISTS', false, ['EXISTS' => null]],
        ];
    }

    /**
     * @param mixed[]|null $repositoryData
     *
     * @dataProvider existsProvider
     */
    public function testExists(
        string $name,
        bool $result,
        array|null $repositoryData,
    ): void {
        $this->configureRepository($repositoryData);
        $this->configureNormalizer([]);

        $this->assertEquals(
            $result,
            Env::exists($name),
        );
    }

    /** @return array<array<mixed>> */
    public function setterProvider(): array
    {
        return [
            ['SET', 'Hello World', 'Hello World'],
            ['SET', 'null', 'null'],
            ['SET', '', ''],
            ['SET', true, 'true'],
            ['SET', false, 'false'],
            ['SET', 3, '3'],
            ['SET', -2, '-2'],
            ['SET', 0, '0'],
            ['SET', 1, '1'],
            ['SET', 3.0, '3.0'],
            ['SET', -2.5, '-2.5'],
            ['SET', 0.0125, '0.0125'],
            ['SET', 1.5, '1.5'],
            ['SET', null, ''],
        ];
    }

    /** @dataProvider setterProvider */
    public function testSet(
        string $name,
        mixed $value,
        string $result,
    ): void {
        $repository = $this->createMock(Repository\RepositoryInterface::class);
        $repository->method('set')
            ->with($name, $result);
        Env::setRepository($repository);

        // Ensure normalizer is not called
        $normalizer = $this->createMock(Normalizer\NormalizerInterface::class);
        Env::setNormalizer($normalizer);

        Env::set($name, $value);
    }

    /** @return array<array<mixed>> */
    public function removeProvider(): array
    {
        return [
            ['REMOVE'],
        ];
    }

    /** @dataProvider removeProvider */
    public function testRemove(string $name): void
    {
        $repository = $this->createMock(Repository\RepositoryInterface::class);
        $repository->method('set')
            ->with($name, null);
        Env::setRepository($repository);

        // Ensure normalizer is not called
        $normalizer = $this->createMock(Normalizer\NormalizerInterface::class);
        Env::setNormalizer($normalizer);

        Env::remove($name);
    }
}
