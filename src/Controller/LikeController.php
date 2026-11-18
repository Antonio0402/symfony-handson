<?php

namespace App\Controller;

use App\Entity\MicroPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(
        MicroPost $post, 
        EntityManagerInterface $entity,
        Request $request
    ): Response
    {
       $currentUser = $this->getUser();
       $post->addLikedBy($currentUser);
       $entity->persist($post);
       $entity->flush();

        return $this->redirectBack($request);

    }
    #[Route('/unlike/{id}', name: 'app_unlike')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unlike(
        MicroPost $post,
        EntityManagerInterface $entity,
        Request $request
    ): Response
    {
        $currentUser = $this->getUser();
        $post->removeLikedBy($currentUser);
        $entity->persist($post);
        $entity->flush();

        
        return $this->redirectBack($request);
    }
    private function redirectBack(Request $request): Response
    {
        $referer = $request->headers->get('referer');

        return $referer
            ? $this->redirect($referer) 
            : $this->redirectToRoute('app_micro_post');
    }
}
