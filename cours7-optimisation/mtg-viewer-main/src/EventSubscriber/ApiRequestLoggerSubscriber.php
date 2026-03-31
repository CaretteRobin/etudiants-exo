<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiRequestLoggerSubscriber implements EventSubscriberInterface
{
    private const REQUEST_START_ATTRIBUTE = '_api_request_started_at';

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isApiRequest($event->getRequest())) {
            return;
        }

        $event->getRequest()->attributes->set(self::REQUEST_START_ATTRIBUTE, microtime(true));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isApiRequest($event->getRequest())) {
            return;
        }

        $request = $event->getRequest();
        $this->logger->info('API request completed', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'query' => $request->query->all(),
            'status' => $event->getResponse()->getStatusCode(),
            'durationMs' => $this->getDurationInMilliseconds($request),
        ]);
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isApiRequest($event->getRequest())) {
            return;
        }

        $request = $event->getRequest();
        $this->logger->error('API request failed', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'query' => $request->query->all(),
            'durationMs' => $this->getDurationInMilliseconds($request),
            'error' => $event->getThrowable()->getMessage(),
        ]);
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    private function getDurationInMilliseconds(Request $request): int
    {
        $startedAt = $request->attributes->get(self::REQUEST_START_ATTRIBUTE);
        if (!is_float($startedAt)) {
            return 0;
        }

        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
