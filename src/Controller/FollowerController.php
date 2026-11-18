<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(
        User $targetUser,
        EntityManagerInterface $entity,
        Request $request
    ): Response
    {   /**  @var User $currentUser */
        $currentUser = $this->getUser();
        if($currentUser->getId() !== $targetUser->getId()) {
            $currentUser->follow($targetUser);
            $entity->persist($currentUser);
            $entity->flush();
        }

        return $this->redirectBack($request);
        
    }
    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(
        User $targetUser,
        EntityManagerInterface $entity,
        Request $request
    ): Response
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if($currentUser->getId() !== $targetUser->getId()) {
            $currentUser->removeFollower($targetUser);
            $entity->persist($currentUser);
            $entity->flush();
        }

        return $this->redirectBack($request);
    }

    private function redirectBack(Request $request) {
        $referer = $request->headers->get('referer');
        return $referer 
        ? $this->redirect($referer)
        : $this->redirectToRoute('app_micro_post');
    }
}
