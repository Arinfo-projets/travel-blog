<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class AdminController extends AbstractController
{
    public function __construct(private PostRepository $postRepository){}

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {

        $posts = $this->postRepository->findBy(array(), array('createdAt'=> 'DESC'));

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'posts' => $posts
        ]);
    }
}
