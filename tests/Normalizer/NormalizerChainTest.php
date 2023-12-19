<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Normalizer;

use IntoTheVoid\Env\Normalizer;
use IntoTheVoid\Env\Test\TestCase;

final class NormalizerChainTest extends TestCase
{
    public function testNormalize(): void
    {
        $innerNormalizerOne = $this->createMock(Normalizer\NormalizerInterface::class);
        $innerNormalizerOne->method('normalize')
            ->withConsecutive(['" Hello "'])
            ->willReturn(' Hello ');

        $innerNormalizerTwo = $this->createMock(Normalizer\NormalizerInterface::class);
        $innerNormalizerTwo->method('normalize')
            ->withConsecutive([' Hello '])
            ->willReturn(' Hello ');

        $innerNormalizerThree = $this->createMock(Normalizer\NormalizerInterface::class);
        $innerNormalizerThree->method('normalize')
            ->withConsecutive([' Hello '])
            ->willReturn('Hello');

        $chain = new Normalizer\NormalizerChain([
            $innerNormalizerOne,
            $innerNormalizerTwo,
            $innerNormalizerThree,
        ]);

        $this->assertEquals(
            'Hello',
            $chain->normalize('" Hello "'),
        );
    }

    public function testEmptyChain(): void
    {
        $chain = new Normalizer\NormalizerChain([]);

        $this->assertEquals(
            'Hello',
            $chain->normalize('Hello'),
        );
        $this->assertEquals(
            ' Hello ',
            $chain->normalize(' Hello '),
        );
        $this->assertEquals(
            '"Hello"',
            $chain->normalize('"Hello"'),
        );
    }
}
