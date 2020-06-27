<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Tests\Unit\Options;

use PHPUnit\Framework\TestCase;
use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOption;
use Symfony\Component\HttpFoundation\Request;

class ApiUrlPrefixOptionTest extends TestCase
{
    public function testIsApiUrl(): void
    {
        $apiUrlPrefixOption = new ApiUrlPrefixOption($this->getOptions());
        $request = $this->getRequest('/api/v1/users');

        self::assertTrue($apiUrlPrefixOption->isApiUrl($request));
    }

    public function testIsNotApiUrl(): void
    {
        $apiUrlPrefixOption = new ApiUrlPrefixOption($this->getOptions());
        $request = $this->getRequest('/api/v2/users');

        self::assertFalse($apiUrlPrefixOption->isApiUrl($request));
    }

    private function getOptions(): array
    {
        return [
            'paths' => [
                'v1' => [
                    'url_path_prefix' => '/api/v1',
                ],
            ],
        ];
    }

    private function getRequest(string $requestUri): Request
    {
        return Request::create($requestUri);
    }
}
