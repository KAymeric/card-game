<?php

namespace App\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api/card')]
class CardController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'card_index', methods: ['GET'])]
    public function index(CardRepository $cardRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $cardsJson = $cache->get('cards_list', function (ItemInterface $item) use ($cardRepository, $serializer) {
            $item->expiresAfter(3600);
            $cards = $cardRepository->findAll();
            return $serializer->serialize($cards, 'json');
        });

        return new JsonResponse($cardsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'card_show', methods: ['GET'])]
    public function show(int $id, CardRepository $cardRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $cardJson = $cache->get('card_' . $id, function (ItemInterface $item) use ($cardRepository, $serializer, $id) {
            $item->expiresAfter(3600);
            $card = $cardRepository->find($id);
            if (!$card) {
                throw new \Exception('Card not found');
            }
            return $serializer->serialize($card, 'json');
        });

        return new JsonResponse($cardJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'card_update', methods: ['PUT'])]
    public function update(int $id, Request $request, CardRepository $cardRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $card = $cardRepository->find($id);

        if (!$card) {
            return new JsonResponse(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        $updatedCard = $serializer->deserialize($data, Card::class, 'json');

        $card->setName($updatedCard->getName());
        $card->setDescription($updatedCard->getDescription());

        $this->entityManager->flush();

        $cache->delete('cards_list');
        $cache->delete('card_' . $id);

        return new JsonResponse(['message' => 'Card updated'], Response::HTTP_OK);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'card_delete', methods: ['DELETE'])]
    public function delete(int $id, CardRepository $cardRepository, CacheInterface $cache): JsonResponse
    {
        $card = $cardRepository->find($id);

        if (!$card) {
            return new JsonResponse(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($card);
        $this->entityManager->flush();

        $cache->delete('cards_list');
        $cache->delete('card_' . $id);

        return new JsonResponse(['message' => 'Card deleted'], Response::HTTP_NO_CONTENT);
    }
}
