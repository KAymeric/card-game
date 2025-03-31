<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\SetRepository;
use App\Entity\Set;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api/set')]
final class SetController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'sets', methods: ['GET'])]
    public function getAll(SetRepository $setRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $setsJson = $cache->get('sets_list', function (ItemInterface $item) use ($setRepository, $serializer) {
            $item->expiresAfter(3600);
            $sets = $setRepository->findAll();
            return $serializer->serialize($sets, 'json');
        });

        return new JsonResponse($setsJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'set_get', methods: ['GET'])]
    public function getOne(int $id, SetRepository $setRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $setJson = $cache->get('set_' . $id, function (ItemInterface $item) use ($setRepository, $serializer, $id) {
            $item->expiresAfter(3600);
            $set = $setRepository->find($id);

            if (!$set) {
                throw new \Exception('Set not found');
            }

            return $serializer->serialize($set, 'json', ['groups' => 'set:read']);
        });

        return new JsonResponse($setJson, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'set_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $set = $serializer->deserialize($data, Set::class, 'json');

        $this->entityManager->persist($set);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'set_update', methods: ['PUT'])]
    public function update(int $id, Request $request, SetRepository $setRepository, CacheInterface $cache, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $set = $serializer->deserialize($data, Set::class, 'json');

        $setToUpdate = $setRepository->find($id);
        $setToUpdate->setName($set->getName());
        $setToUpdate->setCards($set->getCards());
        $setToUpdate->setStats($set->getStats());

        $this->entityManager->flush();
        $cache->delete('set_' . $id);
        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'set_delete', methods: ['DELETE'])]
    public function delete(int $id, SetRepository $setRepository, CacheInterface $cache): JsonResponse
    {
        $set = $setRepository->find($id);
        $set->setStatus('deleted');
        $this->entityManager->flush();
        $cache->delete('sets_list');
        $cache->delete('set_' . $id);


        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
