<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Tests\Unit\EventListener;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use RedRat\ApiHelperBundle\EventListener\RequestApiListener;
use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOption;
use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use function json_encode;

class RequestApiListenerTest extends TestCase
{
    public function testOnKernelRequestConvertData(): void
    {
        $bodyData = [
            'string' => 'string',
            'int' => 10,
            'float' => 12.5,
            'bool' => true,
            'null' => null,
        ];

        $bodyData['array'] = $bodyData;
        $request = $this->getRequest('/api/v1/users', $bodyData);

        self::assertNull($request->get('string'));
        self::assertNull($request->get('int'));
        self::assertNull($request->get('float'));
        self::assertNull($request->get('bool'));
        self::assertNull($request->get('null'));
        self::assertNull($request->get('array'));

        $requestEvent = $this->getRequestEvent($request);
        $apiUrlPrefixOption = $this->getApiUrlPrefixOption();
        $requestApiListener = new RequestApiListener($apiUrlPrefixOption);
        $requestApiListener->onKernelRequest($requestEvent);

        self::assertEquals($bodyData['string'], $request->get('string'));
        self::assertEquals($bodyData['int'], $request->get('int'));
        self::assertEquals($bodyData['float'], $request->get('float'));
        self::assertEquals($bodyData['bool'], $request->get('bool'));
        self::assertEquals($bodyData['null'], $request->get('null'));
        self::assertEquals($bodyData['array'], $request->get('array'));
    }

    public function testOnKernelRequestGetMethod(): void
    {
        $request = $this->getRequest('/api/v1/users', [], RequestMethodInterface::METHOD_GET);
        $requestEvent = $this->getRequestEvent($request);
        $apiUrlPrefixOption = $this->getApiUrlPrefixOption();
        $requestApiListener = new RequestApiListener($apiUrlPrefixOption);
        $requestApiListener->onKernelRequest($requestEvent);

        self::assertTrue(true);
    }

    public function testOnKernelRequestIsNotApiRequest(): void
    {
        $bodyData = [
            'string' => 'string',
            'int' => 10,
            'float' => 12.5,
            'bool' => true,
            'null' => null,
        ];

        $bodyData['array'] = $bodyData;
        $request = $this->getRequest('/api/v3/users', $bodyData);

        $requestEvent = $this->getRequestEvent($request);
        $apiUrlPrefixOption = $this->getApiUrlPrefixOption();
        $requestApiListener = new RequestApiListener($apiUrlPrefixOption);
        $requestApiListener->onKernelRequest($requestEvent);

        self::assertTrue(true);
    }

    public function testOnKernelRequestIsNotValidHeaderContentType(): void
    {
        $bodyData = [
            'string' => 'string',
            'int' => 10,
            'float' => 12.5,
            'bool' => true,
            'null' => null,
        ];

        $bodyData['array'] = $bodyData;
        $request = $this->getRequest(
            '/api/v1/users',
            $bodyData,
            RequestMethodInterface::METHOD_POST,
            'application/x-www-form-urlencoded'
        );

        $requestEvent = $this->getRequestEvent($request);
        $apiUrlPrefixOption = $this->getApiUrlPrefixOption();
        $requestApiListener = new RequestApiListener($apiUrlPrefixOption);
        $requestApiListener->onKernelRequest($requestEvent);
        $response = $requestEvent->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(
            '{"error":"Invalid Content-Type, expected application\/json, got application\/x-www-form-urlencoded"}',
            $response->getContent()
        );
        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testOnKernelRequestIsNotValidBodyData(): void
    {
        $request = $this->getRequestInvalidBody('/api/v1/users');
        $requestEvent = $this->getRequestEvent($request);
        $apiUrlPrefixOption = $this->getApiUrlPrefixOption();
        $requestApiListener = new RequestApiListener($apiUrlPrefixOption);
        $requestApiListener->onKernelRequest($requestEvent);
        $response = $requestEvent->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(
            '{"error":"Invalid json body data"}',
            $response->getContent()
        );
        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEventsExpected = [
            'kernel.request' => [
                ['onKernelRequest', 8],
            ],
        ];

        $subscribedEventsGot = RequestApiListener::getSubscribedEvents();

        self::assertEquals($subscribedEventsExpected, $subscribedEventsGot);
    }

    private function getHttpKernel(): HttpKernelInterface
    {
        return Mockery::mock(HttpKernelInterface::class);
    }

    private function getRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent($this->getHttpKernel(), $request, HttpKernelInterface::MASTER_REQUEST);
    }

    private function getRequest(
        string $requestUri,
        array $bodyData,
        string $method = RequestMethodInterface::METHOD_POST,
        string $contentType = 'application/json'
    ): Request {
        return Request::create(
            $requestUri,
            $method,
            [],
            [],
            [],
            ['CONTENT_TYPE' => $contentType],
            json_encode($bodyData)
        );
    }

    private function getRequestInvalidBody(string $requestUri): Request
    {
        return Request::create(
            $requestUri,
            RequestMethodInterface::METHOD_POST,
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"string":"string","int":10"float":12.5,"bool":true,"null"null}'
        );
    }

    private function getApiUrlPrefixOption(): ApiUrlPrefixOptionInterface
    {
        return new ApiUrlPrefixOption([
            'paths' => [
                'v1' => [
                    'url_path_prefix' => '/api/v1',
                ],
            ],
        ]);
    }
}
