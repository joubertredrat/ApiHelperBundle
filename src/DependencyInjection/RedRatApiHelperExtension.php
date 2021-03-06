<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\DependencyInjection;

use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOption;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RedRatApiHelperExtension extends Extension
{
    public const EXTENSION_ALIAS = 'redrat_api_helper';

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->register(ApiUrlPrefixOption::class);
        $definition->addArgument($config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function getAlias()
    {
        return self::EXTENSION_ALIAS;
    }
}
