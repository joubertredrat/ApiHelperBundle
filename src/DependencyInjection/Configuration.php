<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function method_exists;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('redrat_api_helper');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('redrat_api_helper');
        }

        $rootNode
            ->children()
            ->arrayNode('paths')
                ->useAttributeAsKey('path_name')
                ->normalizeKeys(false)
                ->prototype('array')
                    ->append($this->getUrlPathPrefix())
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getUrlPathPrefix(): ScalarNodeDefinition
    {
        return new ScalarNodeDefinition('url_path_prefix');
    }
}
