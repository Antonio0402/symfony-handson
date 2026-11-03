<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Entity\Comment;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

final class MicroPostController extends AbstractController
{
    #[Route('/micro/post', name: 'app_micro_post')]
    public function index(EntityManagerInterface $em): Response
    {
        // $microPost = new MicroPost();
        // $microPost->setTitle('This is a title from Controller');
        // $microPost->setText('Hello from Controller');
        // $microPost->setCreated(new DateTime());
        
        // $microPost = $em->getRepository(MicroPost::class)->find(1);

        //  if (!$microPost) {
        //     throw $this->createNotFoundException(
        //         'No post found for id 1'
        //     );
        // }

        // $comment = $microPost->getComments()[0];

        // $microPost->removeComment($comment);
        // $em->persist($microPost);
        // $em->flush();


        // $comment = new Comment();
        // $comment->setText('The comment from Controller');
        // $comment->setPost($microPost);
        // $microPost->addComment($comment);

        // tell Doctrine you want to (eventually) save the MicroPost (no queries yet)
        // $em->persist($comment);

        // actually executes the queries (i.e. the INSERT query)
        // $em->flush();


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
    #[Route('/micro/post/add', name: 'app_micro_post_add', priority: 2)]
    public function add(Request $request, EntityManagerInterface $entity): Response
    {
        // $microPost = new MicroPost();
        // $form = $this->createFormBuilder($microPost)
        //     ->add('title', TextType::class)
        //     ->add('text', TextType::class)
        //     ->add('created', DateType::class)
        // ->add('save', SubmitType::class, ['label' => 'Create Post'])
        //     ->getForm();
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            // $newPost->setCreated(new DateTime());
            $entity->persist($newPost);
            $entity->flush();

            $this->addFlash(
                'success',
                "Your new post has been created"
            );

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/micro/post/{post}/edit', name: 'app_micro_post_edit', priority: 2)]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entity): Response
    {
        // $form = $this->createFormBuilder($post)
        //     ->add('title', TextType::class)
        //     ->add('text', TextType::class)
        //     ->add('created', DateType::class)
        //     ->getForm();
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $entity->persist($newPost);
            $entity->flush();

            $this->addFlash(
                'success',
                "Your post has been updated"
            );

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form
        ]);
    }
}
