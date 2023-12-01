<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Test;

use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createMock($originalClassName): MockObject
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->disableAutoReturnValueGeneration()
            ->getMock();
    }
}
