<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(private PostRepository $postRepository){}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $posts = $this->postRepository->findBy(array(), array('createdAt'=> 'DESC'));

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts
        ]);
    }
}
