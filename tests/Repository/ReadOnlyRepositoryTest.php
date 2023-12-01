<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class ReadOnlyRepositoryTest extends TestCase
{
    public function testGet(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepository->method('get')
            ->withConsecutive(['GET'])
            ->willReturn('VALUE');

        $repository = new Repository\ReadOnlyRepository($innerRepository);

        $this->assertEquals(
            'VALUE',
            $repository->get('GET')
        );
    }

    public function testSet(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $repository      = new Repository\ReadOnlyRepository($innerRepository);

        $repository->set('SET', 'VALUE');
    }

    public function testRemove(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $repository      = new Repository\ReadOnlyRepository($innerRepository);

        $repository->set('REMOVE', null);
    }
}
