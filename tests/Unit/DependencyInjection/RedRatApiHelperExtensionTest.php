<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Tests\Unit\DependencyInjection;

use Mockery;
use PHPUnit\Framework\TestCase;
use RedRat\ApiHelperBundle\DependencyInjection\RedRatApiHelperExtension;
use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RedRatApiHelperExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $redRatApiHelperExtension = new RedRatApiHelperExtension();
        $configs = $this->getConfigs();
        $containerBuilder = $this->getContainerBuilder();
        $redRatApiHelperExtension->load($configs, $containerBuilder);

        self::assertTrue(true);
    }

    public function testGetAlias(): void
    {
        $redRatApiHelperExtension = new RedRatApiHelperExtension();

        self::assertEquals(RedRatApiHelperExtension::EXTENSION_ALIAS, $redRatApiHelperExtension->getAlias());
    }

    private function getConfigs(): array
    {
        return [
            'redrat_api_helper' => [
                'paths' => [
                    'v1' => [
                        'url_path_prefix' => '/api/v1',
                    ],
                ],
            ],
        ];
    }

    private function getContainerBuilder(): ContainerBuilder
    {
        $containerBuilderMock = Mockery::mock(ContainerBuilder::class);

        $containerBuilderMock
            ->shouldReceive('register')
            ->withArgs([ApiUrlPrefixOption::class])
            ->andReturn(new Definition())
        ;

        $containerBuilderMock
            ->shouldReceive('fileExists')
            ->andReturn(true)
        ;

        $containerBuilderMock
            ->shouldReceive('setDefinition')
            ->andReturn(new Definition())
        ;

        $containerBuilderMock
            ->shouldReceive('removeBindings')
        ;

        return $containerBuilderMock;
    }
}
