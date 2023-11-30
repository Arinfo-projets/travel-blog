<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends AbstractController
{
    public function __construct(private PostRepository $postRepository){}

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {

        try {
            $this->denyAccessUnlessGranted("ROLE_ADMIN");
        } catch (AccessDeniedException $e) {
            $this->addFlash('warning', 'Access denied. You do not have the required privileges.');
            return $this->redirectToRoute('app_home');
        }

        $posts = $this->postRepository->findBy(array(), array('createdAt'=> 'DESC'));

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'posts' => $posts
        ]);
    }
}
