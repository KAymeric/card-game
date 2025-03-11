<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\StatRepository;
use App\Entity\Stat;
use Symfony\Component\HttpFoundation\Request;

final class StatController extends AbstractController {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/stat', name: 'stats', methods: ['GET'])]
    public function getAll(StatRepository $statRepository, SerializerInterface $serializer): JsonResponse
    {
        $stats = $statRepository->findAll();
        $statsJson = $serializer->serialize($stats, 'json');

        return new JsonResponse($statsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/stat/{id}', name: 'stat_get', methods: ['GET'])]
    public function getOne(int $id, StatRepository $statRepository, SerializerInterface $serializer): JsonResponse
    {
        $stat = $statRepository->find($id);
        $statJson = $serializer->serialize($stat, 'json');

        return new JsonResponse($statJson, Response::HTTP_OK, [], true);
    }

    #[Route('/stat', name: 'stat_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $stat = $serializer->deserialize($data, Stat::class, 'json');

        $this->entityManager->persist($stat);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/stat/{id}', name: 'stat_update', methods: ['PUT'])]
    public function update(int $id, Request $request, StatRepository $statRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $stat = $serializer->deserialize($data, Stat::class, 'json');

        $statToUpdate = $statRepository->find($id);
        $statToUpdate->setName($stat->getName());
        $statToUpdate->setValue($stat->getValue());
        $statToUpdate->setCards($stat->getCards());
        $statToUpdate->setSets($stat->getSets());

        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/stat/{id}', name: 'stat_delete', methods: ['DELETE'])]
    public function delete(int $id, StatRepository $statRepository): JsonResponse
    {
        $stat = $statRepository->find($id);

        $stat->setStatus('deleted');
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}