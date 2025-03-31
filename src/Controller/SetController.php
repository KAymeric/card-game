<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Set;
use App\Repository\SetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api/set')]
class SetController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/', name: 'set_index', methods: ['GET'])]
    public function index(SetRepository $setRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $setsJson = $cache->get('sets_list', function (ItemInterface $item) use ($setRepository, $serializer) {
            $item->expiresAfter(3600);
            $sets = $setRepository->findAll();
            return $serializer->serialize($sets, 'json');
        });

        return new JsonResponse($setsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'set_show', methods: ['GET'])]
    public function show(int $id, SetRepository $setRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {

        $setJson = $cache->get('set_' . $id, function (ItemInterface $item) use ($setRepository, $serializer, $id) {
            $item->expiresAfter(3600);
            $set = $setRepository->find($id);
            if (!$set) {
                throw new \Exception('Set not found');
            }
            return $serializer->serialize($set, 'json', ['groups' => ['set:read', 'card:read', 'type:read']]);
        });

        return new JsonResponse($setJson, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'set_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $data = $request->getContent();
        $set = $serializer->deserialize($data, Set::class, 'json');

        $this->entityManager->persist($set);
        $this->entityManager->flush();

        $cache->delete('sets_list');

        return new JsonResponse(['message' => 'Set created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'set_update', methods: ['PUT'])]
    public function update(int $id, Request $request, SetRepository $setRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $set = $setRepository->find($id);

        if (!$set) {
            return new JsonResponse(['error' => 'Set not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        $updatedSet = $serializer->deserialize($data, Set::class, 'json');

        $set->setName($updatedSet->getName());
        $set->setImage($updatedSet->getImage());

        $this->entityManager->flush();

        $cache->delete('sets_list');
        $cache->delete('set_' . $id);

        return new JsonResponse(['message' => 'Set updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'set_delete', methods: ['DELETE'])]
    public function delete(int $id, SetRepository $setRepository, CacheInterface $cache): JsonResponse
    {
        $set = $setRepository->find($id);

        if (!$set) {
            return new JsonResponse(['error' => 'Set not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($set);
        $this->entityManager->flush();

        $cache->delete('sets_list');
        $cache->delete('set_' . $id);

        return new JsonResponse(['message' => 'Set deleted'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/{setId}/card', name: 'create_card_for_set', methods: ['POST'])]
    public function createCardForSet(int $setId, Request $request, SetRepository $setRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, CacheInterface $cache): JsonResponse
    {
        // Récupérer l'ensemble (Set) par ID
        $set = $setRepository->find($setId);

        if (!$set) {
            return new JsonResponse(['error' => 'Set not found'], Response::HTTP_NOT_FOUND);
        }

        // Désérialiser les données pour créer la carte
        $data = $request->getContent();
        $card = $serializer->deserialize($data, Card::class, 'json');

        // Assigner l'ensemble (Set) à la carte
        $card->setSet($set);

        // Sauvegarder la carte
        $entityManager->persist($card);
        $entityManager->flush();
        $cache->delete('sets_list');
        $cache->delete('set_' . $setId);
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
