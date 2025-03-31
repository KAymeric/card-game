<?php

namespace App\EventListener;

use App\Service\GlobalStatsService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener
{
    private $globalStatsService;

    public function __construct(GlobalStatsService $globalStatsService)
    {
        $this->globalStatsService = $globalStatsService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if ($route) {
            $this->globalStatsService->incrementRouteCount($route);
        }

        //get the current user device and store it in the session

        $userAgent = $request->headers->get('User-Agent');
        if ($userAgent) {
            $this->globalStatsService->incrementUserAgent($userAgent);
        }

        $this->globalStatsService->incrementTotalRequest();
    }
}
