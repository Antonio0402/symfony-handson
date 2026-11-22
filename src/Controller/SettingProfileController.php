<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SettingProfileController extends AbstractController
{
    #[Route('/setting/profile', name: 'app_setting_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, EntityManagerInterface $entity): Response
    {   /**
        * @var User $user;
        */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(
            UserProfileType::class,
            $userProfile
        );

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $entity->persist($user);
            $entity->flush();
            
            $this->addFlash(
                'success',
                'Your user profile settings were saved.'
            );

            return $this->redirectToRoute('app_setting_profile');
        }
        return $this->render('setting_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/setting/profile-image', name: 'app_setting_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(): Response
    {
        $form = $this->createForm(ProfileImageType::class);
        return $this->render('setting_profile/profile_image.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
