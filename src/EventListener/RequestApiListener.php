<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function json_decode;
use function json_last_error;
use function sprintf;
use const JSON_ERROR_NONE;

class RequestApiListener implements EventSubscriberInterface
{
    public const CONTENT_TYPE_JSON = 'application/json';
    public const REQUEST_CONTENT_TYPE_JSON = 'json';

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 10],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

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
        }

        if (!$this->isValidBodyData($request)) {
            $event->setResponse(
                $this->getResponse('Invalid json data')
            );
        }

        $this->convertData($request);
    }

    private function isValidHeaderContentType(Request $request): bool
    {
        return self::REQUEST_CONTENT_TYPE_JSON === $request->getContentType();
    }

    private function isValidBodyData(Request $request): bool
    {
        json_decode($request->getContent(), true);

        return JSON_ERROR_NONE == json_last_error();
    }

    private function getResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }

    private function convertData(Request $request): void
    {
        $jsonData = json_decode($request->getContent(), true);

        $request
            ->request
            ->replace($jsonData)
        ;
    }
}
