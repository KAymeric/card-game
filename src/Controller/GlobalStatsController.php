<?php

namespace App\Controller;

use App\Service\GlobalStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/global-stats')]
final class GlobalStatsController extends AbstractController
{
    private $globalStatsService;

    public function __construct(GlobalStatsService $globalStatsService)
    {
        $this->globalStatsService = $globalStatsService;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $aggregatedStats = $this->globalStatsService->getStats();
        return new JsonResponse($aggregatedStats);
    }
}
