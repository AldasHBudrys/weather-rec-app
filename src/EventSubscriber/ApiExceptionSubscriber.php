<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof \InvalidArgumentException) {
            $response = new JsonResponse([
                'status' => 404,
                'error' => $exception->getMessage(),
                'code' => 'INVALID_CITY',
            ], 404);

            $event->setResponse($response);
            return;
        }

        if ($exception instanceof \RuntimeException) {
            $response = new JsonResponse([
                'status' => 503,
                'error' => 'External service error',
                'code' => 'WEATHER_SERVICE_UNAVAILABLE',
            ], 503);

            $event->setResponse($response);
            return;
        }

        $response = new JsonResponse([
            'status' => 500,
            'error' => 'Unexpected server error',
            'code' => 'INTERNAL_ERROR',
        ], 500);

        $event->setResponse($response);
    }
}
