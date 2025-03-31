<?php


namespace App\Controller;

use App\Entity\CustomMedia;
use App\Repository\CustomMediaRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/api/media')]
final class CustomMediaController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'media_list', methods: ['GET'])]
    public function getAll(CustomMediaRepository $mediaRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $mediaJson = $cache->get('media_list', function (ItemInterface $item) use ($mediaRepository, $serializer) {
            $item->expiresAfter(3600);
            $media = $mediaRepository->findAll();
            return $serializer->serialize($media, 'json');
        });

        return new JsonResponse($mediaJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'media_get', methods: ['GET'])]
    public function getOne(int $id, CustomMediaRepository $mediaRepository, SerializerInterface $serializer, CacheInterface $cache): JsonResponse
    {
        $mediaJson = $cache->get('media_' . $id, function (ItemInterface $item) use ($mediaRepository, $serializer, $id) {
            $item->expiresAfter(3600);
            $media = $mediaRepository->find($id);

            if (!$media) {
                throw new \Exception('Media not found');
            }

            return $serializer->serialize($media, 'json');
        });

        return new JsonResponse($mediaJson, Response::HTTP_OK, [], true);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'media_upload', methods: ['POST'])]
    public function upload(Request $request, CacheInterface $cache): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'video/mp4'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return new JsonResponse(['error' => 'Invalid file type'], Response::HTTP_BAD_REQUEST);
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDir, $filename);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'File upload error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $media = (new CustomMedia())
            ->setPath('/publics/uploads')
            ->setRealname($file->getClientOriginalName())
            ->setMimeType($file->getClientMimeType())
            ->setFile($file)
            ->setStatus('active')
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        $cache->delete('media_list');

        return new JsonResponse(['message' => 'File uploaded', 'path' => $media->getPath()], Response::HTTP_CREATED);
    }
}
