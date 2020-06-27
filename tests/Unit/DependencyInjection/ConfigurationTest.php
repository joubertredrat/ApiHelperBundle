<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RedRat\ApiHelperBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();

        self::assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }
}
