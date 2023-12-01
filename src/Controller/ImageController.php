<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Post;
use App\Form\Image1Type;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\ImageDto;

#[Route('/image')]
class ImageController extends AbstractController
{
    public function __construct(private PictureService $pictureService, private ImageRepository $imageRepository)
    {
    }

    #[Route('/', name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/post/{post}', name: '', methods: ['GET'])]
    public function getImageByPostId(Request $request): JsonResponse
    {
        $postId = $request->get('post');

        $images = $this->imageRepository->findBy(['post' => $postId]);

        $imageDtos = [];

        foreach ($images as $image) {
            $imageDtos[] = new ImageDto($image->getId(), $image->getFilePath());
        }

        return $this->json($this->json($imageDtos)->getContent(), 200, [], ['groups' => 'post']);
    }

    #[Route('/{id}', name: 'app_image_delete', methods: ['delete'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {

            $entityManager->remove($image);

            $this->addFlash('success', 'Image supprimé');
            $this->pictureService->delete($image->getFilePath(), 'post');

            $entityManager->flush();


        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
