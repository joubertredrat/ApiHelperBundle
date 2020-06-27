<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\EventListener;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use RedRat\ApiHelperBundle\Options\ApiUrlPrefixOptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function in_array;
use function json_decode;
use function json_last_error;
use function sprintf;
use const JSON_ERROR_NONE;

class RequestApiListener implements EventSubscriberInterface
{
    public const CONTENT_TYPE_JSON = 'application/json';
    public const REQUEST_CONTENT_TYPE_JSON = 'json';

    private ApiUrlPrefixOptionInterface $apiUrlPrefixOption;

    public function __construct(ApiUrlPrefixOptionInterface $apiUrlPrefixOption)
    {
        $this->apiUrlPrefixOption = $apiUrlPrefixOption;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 8],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->isApiRequest($request)) {
            return;
        }

        if (!$this->isValidHeaderContentType($request)) {
            $event->setResponse(
                $this->getResponse(
                    sprintf(
                        'Invalid Content-Type, expected %s, got %s',
                        self::CONTENT_TYPE_JSON,
                        $request
                            ->headers
                            ->get('CONTENT_TYPE')
                        ,
                    )
                )
            );

            return;
        }

        if (!$this->isValidBodyData($request)) {
            $event->setResponse(
                $this->getResponse('Invalid json data')
            );

            return;
        }

        $this->convertData($request);
    }

    private function isApiRequest(Request $request): bool
    {
        return $this
            ->apiUrlPrefixOption
            ->isApiUrl($request)
        ;
    }

    private function isValidHeaderContentType(Request $request): bool
    {
        if (in_array($request->getMethod(), $this->getMethodsNoBody())) {
            return true;
        }

        return self::REQUEST_CONTENT_TYPE_JSON === $request->getContentType();
    }

    private function getMethodsNoBody(): array
    {
        return [
            RequestMethodInterface::METHOD_GET,
            RequestMethodInterface::METHOD_DELETE,
        ];
    }

    private function isValidBodyData(Request $request): bool
    {
        json_decode($request->getContent(), true);

        return JSON_ERROR_NONE == json_last_error();
    }

    private function getResponse(
        string $message,
        int $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST
    ): JsonResponse {
        return new JsonResponse(['error' => $message], $statusCode);
    }

    private function convertData(Request $request): void
    {
        $jsonData = json_decode($request->getContent(), true);

        if ($jsonData) {
            $request
                ->request
                ->replace($jsonData)
            ;
        }
    }
}
