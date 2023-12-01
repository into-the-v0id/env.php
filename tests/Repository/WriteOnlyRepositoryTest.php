<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test\Repository;

use IntoTheVoid\Env\Repository;
use IntoTheVoid\Env\Test\TestCase;

final class WriteOnlyRepositoryTest extends TestCase
{
    public function testGet(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $repository      = new Repository\WriteOnlyRepository($innerRepository);

        $this->assertEquals(
            null,
            $repository->get('GET')
        );
    }

    public function testSet(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepository->method('set')
            ->withConsecutive(['SET', 'VALUE']);

        $repository = new Repository\WriteOnlyRepository($innerRepository);

        $repository->set('SET', 'VALUE');
    }

    public function testRemove(): void
    {
        $innerRepository = $this->createMock(Repository\RepositoryInterface::class);
        $innerRepository->method('set')
            ->withConsecutive(['REMOVE', null]);

        $repository = new Repository\WriteOnlyRepository($innerRepository);

        $repository->set('REMOVE', null);
    }
}
