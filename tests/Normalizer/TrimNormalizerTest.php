<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Normalizer;

use IntoTheVoid\Env\Normalizer;
use IntoTheVoid\Env\Test\TestCase;

final class TrimNormalizerTest extends TestCase
{
    private static function createNormalizer(): Normalizer\TrimNormalizer
    {
        return new Normalizer\TrimNormalizer();
    }

    /**
     * @return array<array<mixed>>
     */
    public function normalizeProvider(): array
    {
        return [
            ['Hello', 'Hello'],
            [' Hello', 'Hello'],
            [' Hello ', 'Hello'],
            ['     Hello  ', 'Hello'],
            ['  Hello  Hello  ', 'Hello  Hello'],
        ];
    }

    /**
     * @dataProvider normalizeProvider
     */
    public function testNormalize(string $value, string $result): void
    {
        $this->assertEquals(
            $result,
            self::createNormalizer()->normalize($value)
        );
    }
}
