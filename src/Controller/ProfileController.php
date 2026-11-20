<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show(User $user, EntityManagerInterface $entityManager): Response
    {   
        $posts = $entityManager->getRepository(MicroPost::class);
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'post' => $posts->findAllByAuthor($user)
        ]);
    }
    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(User $user): Response
    {
       
        return $this->render('profile/follows.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('profile/followers.html.twig', [
            'user' => $user
        ]);
    }
}
