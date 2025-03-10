<?php

namespace App\Controller;

use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Card;
use Doctrine\ORM\EntityManager;

final class CardController extends AbstractController
{
    #[Route('/card', name: 'app_cards', methods: ['GET'])]
    public function getAll(CardRepository $cardRepository, SerializerInterface $serializer): JsonResponse
    {
        $cards = $cardRepository->findAll();
        $cardsJson = $serializer->serialize($cards, 'json');

        return new JsonResponse($cardsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/card/{id}', name: 'app_card_get', methods: ['GET'])]
    public function getOne(int $id, CardRepository $cardRepository, SerializerInterface $serializer): JsonResponse
    {
        $card = $cardRepository->find($id);
        $cardJson = $serializer->serialize($card, 'json');

        return new JsonResponse($cardJson, Response::HTTP_OK, [], true);
    }

    #[Route('/card', name: 'app_card_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManager $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $card = $serializer->deserialize($data, Card::class, 'json');

        $entityManager->persist($card);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/card/{id}', name: 'app_card_update', methods: ['PUT'])]
    public function update(int $id, Request $request, CardRepository $cardRepository, SerializerInterface $serializer, EntityManager $entityManager): JsonResponse
    {
        $data = $request->getContent();
        $card = $serializer->deserialize($data, Card::class, 'json');

        $cardToUpdate = $cardRepository->find($id);
        $cardToUpdate->setName($card->getName());
        $cardToUpdate->setSetId($card->getSetId());
        $cardToUpdate->setTypeId($card->getTypeId());
        $cardToUpdate->setStats($card->getStats());

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/card/{id}', name: 'app_card_delete', methods: ['DELETE'])]
    public function delete(int $id, CardRepository $cardRepository, EntityManager $entityManager): JsonResponse
    {
        $card = $cardRepository->find($id);

        $card->setStatus('deleted');
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
