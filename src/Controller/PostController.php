<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Image;
use App\Service\PictureService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\PostDto;

#[Route('/post')]
class PostController extends AbstractController
{

    public function __construct(private PictureService $pictureService){}

    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach($images as $image){
                // On définit le dossier de destination
                $folder = 'post';

                // On appelle le service d'ajout
                $fichier = $this->pictureService->add($image, $folder, 300, 300);


                $img = new Image();
                $img->setFilePath($fichier);
                $post->addImage($img);

            }

            $entityManager->persist($post);
            $entityManager->flush();


            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted("ROLE_ADMIN");
        } catch (AccessDeniedException $e) {
            $this->addFlash('warning', 'Access denied. You do not have the required privileges.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            foreach($images as $image){
                // On définit le dossier de destination
                $folder = 'post';

                // On appelle le service d'ajout
                $fichier = $this->pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setFilePath($fichier);
                $post->addImage($img);

            }

            $entityManager->flush();

            $this->addFlash('success','Post modifié');

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        $this->addFlash('success','Post suprimé');

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }
}
