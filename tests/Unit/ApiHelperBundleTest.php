<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RedRat\ApiHelperBundle\ApiHelperBundle;
use RedRat\ApiHelperBundle\DependencyInjection\RedRatApiHelperExtension;

class ApiHelperBundleTest extends TestCase
{
    public function testGetContainerExtension(): void
    {
        $apiHelperBundle = new ApiHelperBundle();

        self::assertInstanceOf(RedRatApiHelperExtension::class, $apiHelperBundle->getContainerExtension());
    }
}
