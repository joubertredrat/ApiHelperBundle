<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Options;

use Symfony\Component\HttpFoundation\Request;
use function array_map;
use function filter_var;
use function preg_quote;
use function sprintf;

class ApiUrlPrefixOption implements ApiUrlPrefixOptionInterface
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function isApiUrl(Request $request): bool
    {
        foreach ($this->getUrlPathsPrefix() as $urlPathPrefix) {
            if ($this->urlMatches($request, $urlPathPrefix)) {
                return true;
            }
        }

        return false;
    }

    private function urlMatches(Request $request, string $urlPathPrefix): bool
    {
        return filter_var(
            $request->getRequestUri(),
            FILTER_VALIDATE_REGEXP,
            ['options' => ['regexp' => $this->getUrlPathPrefixRegex($urlPathPrefix)]]) !== false
        ;
    }

    private function getUrlPathPrefixRegex(string $urlPathPrefix): string
    {
        return sprintf(
            '/^(%s)/',
            preg_quote($urlPathPrefix, '/'),
        );
    }

    private function getUrlPathsPrefix(): array
    {
        return array_map(function($item) {
            return $item['url_path_prefix'];
        }, $this->options['paths']);
    }
}
