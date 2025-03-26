<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\SetRepository;
use App\Entity\Set;
use Symfony\Component\HttpFoundation\Request;

final class SetController extends AbstractController {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/set', name: 'sets', methods: ['GET'])]
    public function getAll(SetRepository $setRepository, SerializerInterface $serializer): JsonResponse
    {
        $sets = $setRepository->findAll();
        $setsJson = $serializer->serialize($sets, 'json');

        return new JsonResponse($setsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/set/{id}', name: 'set_get', methods: ['GET'])]
    public function getOne(int $id, SetRepository $setRepository, SerializerInterface $serializer): JsonResponse
    {
        $set = $setRepository->find($id);
        $setJson = $serializer->serialize($set, 'json');

        return new JsonResponse($setJson, Response::HTTP_OK, [], true);
    }

    #[Route('/set', name: 'set_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $set = $serializer->deserialize($data, Set::class, 'json');

        $this->entityManager->persist($set);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/set/{id}', name: 'set_update', methods: ['PUT'])]
    public function update(int $id, Request $request, SetRepository $setRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $set = $serializer->deserialize($data, Set::class, 'json');

        $setToUpdate = $setRepository->find($id);
        $setToUpdate->setName($set->getName());
        $setToUpdate->setCards($set->getCards());
        $setToUpdate->setStats($set->getStats());

        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/set/{id}', name: 'set_delete', methods: ['DELETE'])]
    public function delete(int $id, SetRepository $setRepository): JsonResponse
    {
        $set = $setRepository->find($id);

        $set->setStatus('deleted');
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}