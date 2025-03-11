<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\TypeRepository;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\Request;

final class TypeController extends AbstractController {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/type', name: 'types', methods: ['GET'])]
    public function getAll(TypeRepository $typeRepository, SerializerInterface $serializer): JsonResponse
    {
        $types = $typeRepository->findAll();
        $typesJson = $serializer->serialize($types, 'json');

        return new JsonResponse($typesJson, Response::HTTP_OK, [], true);
    }

    #[Route('/type/{id}', name: 'type_get', methods: ['GET'])]
    public function getOne(int $id, TypeRepository $typeRepository, SerializerInterface $serializer): JsonResponse
    {
        $type = $typeRepository->find($id);
        $typeJson = $serializer->serialize($type, 'json');

        return new JsonResponse($typeJson, Response::HTTP_OK, [], true);
    }

    #[Route('/type', name: 'type_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $type = $serializer->deserialize($data, Type::class, 'json');

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/type/{id}', name: 'type_update', methods: ['PUT'])]
    public function update(int $id, Request $request, TypeRepository $typeRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $type = $serializer->deserialize($data, Type::class, 'json');

        $typeToUpdate = $typeRepository->find($id);
        $typeToUpdate->setName($type->getName());
        $typeToUpdate->setImageId($type->getImageId());

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/type/{id}', name: 'type_delete', methods: ['DELETE'])]
    public function delete(int $id, TypeRepository $typeRepository): JsonResponse
    {
        $type = $typeRepository->find($id);

        $type->setStatus('deleted');
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
