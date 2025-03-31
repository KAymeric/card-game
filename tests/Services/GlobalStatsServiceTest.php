<?php

namespace App\Tests\Services;

use App\Entity\GlobalStats;
use App\Service\GlobalStatsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class GlobalStatsServiceTest extends TestCase
{
    public function testIncrementRouteCountCreatesNewStat()
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'route.test'])
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(GlobalStats::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(GlobalStats::class));
        $entityManager->expects($this->once())
            ->method('flush');

        $service = new GlobalStatsService($entityManager);
        $service->incrementRouteCount("test");
    }

    public function testGetStatsAggregatesCorrectly()
    {
        $stat1 = new GlobalStats();
        $stat1->setKey("route.api_doc");
        $stat1->setValue("3");

        $stat2 = new GlobalStats();
        $stat2->setKey("route.index");
        $stat2->setValue("2");

        $stat3 = new GlobalStats();
        $stat3->setKey("total_request");
        $stat3->setValue("5");

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$stat1, $stat2, $stat3]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(GlobalStats::class)
            ->willReturn($repository);

        $service = new GlobalStatsService($entityManager);
        $stats = $service->getStats();

        $expected = [
            "route" => [
                "api_doc" => 3,
                "index"   => 2,
            ],
            "total_request" => [
                "value"   => 5
            ]
        ];

        $this->assertEquals($expected, $stats);
    }
}
