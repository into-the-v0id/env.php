<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class RepositoryChainTest extends TestCase
{
    public function testGetUnsetVariable(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('get')
            ->with('GET_UNSET')
            ->willReturn(null);

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryTwo->method('get')
            ->with('GET_UNSET')
            ->willReturn(null);

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $this->assertNull($chain->get('GET_UNSET'));
    }

    public function testGetEmptyVariable(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('get')
            ->with('GET_EMPTY')
            ->willReturn('');

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $this->assertEquals(
            '',
            $chain->get('GET_EMPTY'),
        );
    }

    public function testGet(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('get')
            ->with('GET_STRING')
            ->willReturn(null);

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryTwo->method('get')
            ->with('GET_STRING')
            ->willReturn('Hello World');

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $this->assertEquals(
            'Hello World',
            $chain->get('GET_STRING'),
        );
    }

    public function testSetToEmpty(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('set')
            ->with('SET_EMPTY', '');

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryTwo->method('set')
            ->with('SET_EMPTY', '');

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $chain->set('SET_EMPTY', '');
    }

    public function testSet(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('set')
            ->with('SET_STRING', 'Hello World');

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryTwo->method('set')
            ->with('SET_STRING', 'Hello World');

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $chain->set('SET_STRING', 'Hello World');
    }

    public function testRemove(): void
    {
        $innerRepositoryOne = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryOne->method('set')
            ->with('REMOVE', null);

        $innerRepositoryTwo = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepositoryTwo->method('set')
            ->with('REMOVE', null);

        $chain = new Repository\RepositoryChain([
            $innerRepositoryOne,
            $innerRepositoryTwo,
        ]);

        $chain->set('REMOVE', null);
    }

    public function testEmptyChain(): void
    {
        $chain = new Repository\RepositoryChain([]);

        $this->assertNull($chain->get('GET_UNSET'));
        $chain->set('SET_STRING', 'Hello World');
        $this->assertNull($chain->get('SET_STRING'));
        $chain->set('UNSET', null);
    }
}
