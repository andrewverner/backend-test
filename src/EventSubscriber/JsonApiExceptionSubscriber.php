<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\ApplicationExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final readonly class JsonApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -10]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ApplicationExceptionInterface) {
            $event->setResponse(new JsonResponse(
                data: ['errors' => $exception->getErrors()],
                status: $exception->getStatus(),
            ));

            return;
        }

        $responseData = $this->formatException($exception);
        $status = $responseData['errors'][0]['status'] ?? 500;

        $event->setResponse(new JsonResponse($responseData, (int)$status));
    }

    private function formatException(Throwable $e): array
    {
        $status = 500;
        $title = 'Internal Server Error';
        $detail = $e->getMessage() ?: 'An unexpected error occurred';

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $title = match ($status) {
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                409 => 'Conflict',
                default => 'HTTP Error',
            };
        } elseif ($e instanceof \InvalidArgumentException) {
            $status = 400;
            $title = 'Invalid Argument';
        } elseif ($e instanceof \DomainException) {
            $status = 422;
            $title = 'Domain Error';
        }

        return [
            'errors' => [[
                'status' => (string) $status,
                'title' => $title,
                'detail' => $detail,
            ]],
        ];
    }
}
