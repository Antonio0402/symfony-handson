<?php

namespace App\Controller;

use App\Entity\MicroPost;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class MicroPostController extends AbstractController
{
    #[Route('/micro/post', name: 'app_micro_post')]
    public function index(EntityManagerInterface $em): Response
    {
        // $microPost = new MicroPost();
        // $microPost->setTitle('This is a title from Controller');
        // $microPost->setText('Hello from Controller');
        // $microPost->setCreated(new DateTime());

        // tell Doctrine you want to (eventually) save the MicroPost (no queries yet)
        // $em->persist($microPost);

        // actually executes the queries (i.e. the INSERT query)
        // $em->flush();

        // $microPost = $posts->find(7);

        // Delete existing
        // if ($microPost = $posts->find(7)) {
        //     $em->remove($microPost);
        //     $em->flush();
        // }

        // dd($posts->findBy(['title' => 'Welcome to US!']));

        $posts = $em->getRepository(MicroPost::class);

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }

    #[Route('/micro/post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {
        // dd($post);

        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);

    }
}
