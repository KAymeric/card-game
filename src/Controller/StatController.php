<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\StatRepository;
use App\Entity\Stat;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/stat')]
final class StatController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'stats', methods: ['GET'])]
    public function getAll(StatRepository $statRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $statsJson = $cache->get('stats_list', function (ItemInterface $item) use ($statRepository, $serializer) {
            $item->expiresAfter(3600); // Cache pendant 1 heure
            $stats = $statRepository->findAll();
            return $serializer->serialize($stats, 'json');
        });

        return new JsonResponse($statsJson, Response::HTTP_OK, [], true);
    }
    public function getOne(int $id, StatRepository $statRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $statJson = $cache->get('stat_' . $id, function (ItemInterface $item) use ($statRepository, $serializer, $id) {
            $item->expiresAfter(3600); // Cache pendant 1 heure
            $stat = $statRepository->find($id);

            if (!$stat) {
                throw new \Exception('Stat not found');
            }

            return $serializer->serialize($stat, 'json');
        });

        return new JsonResponse($statJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'stat_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $data = $request->getContent();
        $stat = $serializer->deserialize($data, Stat::class, 'json');

        $this->entityManager->persist($stat);
        $this->entityManager->flush();

        $cache->delete('stats_list');

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'stat_update', methods: ['PUT'])]
    public function update(int $id, Request $request, StatRepository $statRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $data = $request->getContent();
        $stat = $serializer->deserialize($data, Stat::class, 'json');

        $statToUpdate = $statRepository->find($id);
        $statToUpdate->setName($stat->getName());
        $statToUpdate->setValue($stat->getValue());
        $statToUpdate->setCards($stat->getCards());
        $statToUpdate->setSets($stat->getSets());

        $this->entityManager->flush();

        $cache->delete('stat_' . $id);
        $cache->delete('stats_list');

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'stat_delete', methods: ['DELETE'])]
    public function delete(int $id, StatRepository $statRepository, CacheInterface $cache): JsonResponse
    {
        $stat = $statRepository->find($id);

        $stat->setStatus('deleted');
        $this->entityManager->flush();

        $cache->delete('stat_' . $id);
        $cache->delete('stats_list');

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
