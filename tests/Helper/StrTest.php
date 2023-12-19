<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Helper;

use IntoTheVoid\Env\Helper\Str;
use IntoTheVoid\Env\Test\TestCase;

final class StrTest extends TestCase
{
    /** @return array<array<mixed>> */
    public function startsWithProvider(): array
    {
        return [
            ['Hello', 'Hello', true],
            ['Hello', 'Hel', true],
            ['Hello', 'H', true],
            ['Hello', '', true],
            ['Hello', 'B', false],
            ['äúç', 'ä', true],
            ['äúç', 'ç', false],
            ['', '', true],
        ];
    }

    /** @dataProvider startsWithProvider */
    public function testStartsWith(string $haystack, string $needle, bool $result): void
    {
        $this->assertEquals(
            $result,
            Str::startsWith($haystack, $needle),
        );
    }

    /** @return array<array<mixed>> */
    public function endsWithProvider(): array
    {
        return [
            ['Hello', 'Hello', true],
            ['Hello', 'llo', true],
            ['Hello', 'o', true],
            ['Hello', '', true],
            ['Hello', 'B', false],
            ['äúç', 'ç', true],
            ['äúç', 'ä', false],
            ['', '', true],
        ];
    }

    /** @dataProvider endsWithProvider */
    public function testEndsWith(string $haystack, string $needle, bool $result): void
    {
        $this->assertEquals(
            $result,
            Str::endsWith($haystack, $needle),
        );
    }

    /** @return array<array<mixed>> */
    public function containsProvider(): array
    {
        return [
            ['Hello', 'Hello', true],
            ['Hello', 'llo', true],
            ['Hello', 'ell', true],
            ['Hello', 'H', true],
            ['Hello', '', true],
            ['Hello', 'B', false],
            ['äúç', 'ç', true],
            ['äúç', 'ú', true],
            ['äç', 'ú', false],
            ['', '', true],
        ];
    }

    /** @dataProvider containsProvider */
    public function testContains(string $haystack, string $needle, bool $result): void
    {
        $this->assertEquals(
            $result,
            Str::contains($haystack, $needle),
        );
    }

    /** @return array<array<mixed>> */
    public function unwrapProvider(): array
    {
        return [
            ['(Hello)', '(', ')', 'Hello'],
            ['Hello', 'H', 'o', 'ell'],
            [' Hello ', ' ', ' ', 'Hello'],
            ['||Hello||', '|', '|', '|Hello|'],
            ['Hello', '', '', 'Hello'],
            ['Hello', 'H', '', 'ello'],
            ['Hello', '', 'o', 'Hell'],
            ['Hello', 'He', 'lo', 'l'],
            ['Hello', 'Hell', 'o', ''],
            ['Hello', 'Hello', 'Hello', 'Hello'],
            ['Hello', 'A', 'Z', 'Hello'],
            ['', 'A', 'Z', ''],
            ['', '', '', ''],
            ['äúç', 'ä', 'ç', 'ú'],
        ];
    }

    /** @dataProvider unwrapProvider */
    public function testUnwrap(string $value, string $left, string $right, string $result): void
    {
        $this->assertEquals(
            $result,
            Str::unwrap($value, $left, $right),
        );
    }

    /** @return array<array<mixed>> */
    public function splitProvider(): array
    {
        return [
            ['Hello', '', ['H', 'e', 'l', 'l', 'o']],
            ['äúç', '', ['ä', 'ú', 'ç']],
            ['Hello', 'World', ['Hello']],
            ['äúç', 'ñåï', ['äúç']],
            ['Hello', 'l', ['He', '', 'o']],
            ['äúç', 'ä', ['', 'úç']],
            ['Hello', 'ell', ['H', 'o']],
            ['äúç', 'úç', ['ä', '']],
            ['', '', ['']],
            ['', 'abc', ['']],
        ];
    }

    /**
     * @param string[] $result
     *
     * @dataProvider splitProvider
     */
    public function testSplit(string $value, string $separator, array $result): void
    {
        $this->assertEquals(
            $result,
            Str::split($value, $separator),
        );
    }

    /** @return array<array<mixed>> */
    public function fromProvider(): array
    {
        return [
            ['Hello World', 'Hello World'],
            [true, 'true'],
            [false, 'false'],
            [3, '3'],
            [-2, '-2'],
            [0, '0'],
            [1, '1'],
            [3.0, '3.0'],
            [-2.5, '-2.5'],
            [0.0125, '0.0125'],
            [1.5, '1.5'],
            [null, ''],
        ];
    }

    /** @dataProvider fromProvider */
    public function testFrom(mixed $value, string $result): void
    {
        $this->assertEquals(
            $result,
            Str::from($value),
        );
    }
}
