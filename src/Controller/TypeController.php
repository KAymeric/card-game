<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\TypeRepository;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api/type')]

final class TypeController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'types', methods: ['GET'])]
    public function getAll(TypeRepository $typeRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $typesJson = $cache->get('types_list', function (ItemInterface $item) use ($typeRepository, $serializer) {
            $item->expiresAfter(3600);
            $types = $typeRepository->findAll();
            return $serializer->serialize($types, 'json');
        });

        return new JsonResponse($typesJson, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'type_get', methods: ['GET'])]
    public function getOne(int $id, TypeRepository $typeRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $typeJson = $cache->get('type_' . $id, function (ItemInterface $item) use ($typeRepository, $serializer, $id) {
            $item->expiresAfter(3600); // Cache valid for 1 hour
            $type = $typeRepository->find($id);

            if (!$type) {
                throw new \Exception('Type not found');
            }

            return $serializer->serialize($type, 'json');
        });

        return new JsonResponse($typeJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'type_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $data = $request->getContent();
        $type = $serializer->deserialize($data, Type::class, 'json');

        $type->updatedAt = (new \DateTimeImmutable());

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        $cache->delete('types_list');

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'type_update', methods: ['PUT'])]
    public function update(int $id, Request $request, TypeRepository $typeRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $data = $request->getContent();
        $type = $serializer->deserialize($data, Type::class, 'json');

        $typeToUpdate = $typeRepository->find($id);
        $typeToUpdate->setName($type->getName());
        $typeToUpdate->setImageId($type->getImageId());

        $this->entityManager->flush();

        $cache->delete('type_' . $id);
        $cache->delete('types_list');

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'type_delete', methods: ['DELETE'])]
    public function delete(int $id, TypeRepository $typeRepository, CacheInterface $cache): JsonResponse
    {
        $type = $typeRepository->find($id);

        $type->setStatus('deleted');
        $this->entityManager->flush();

        $cache->delete('type_' . $id);
        $cache->delete('types_list');

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
