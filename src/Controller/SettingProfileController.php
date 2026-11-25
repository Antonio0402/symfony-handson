<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function profileImage(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $entity
    ): Response
    {
        $form = $this->createForm(ProfileImageType::class);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $profileImageFile = $form->get('profileImage')->getData();
            
        // this condition is needed because the 'profileImage' field is not required
        // so the Image file must be processed only when a file is uploaded

            if($profileImageFile) {
                $originalFileName = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                // this is needed to safely include the file name as part of the URL
                $safeFileName = $slugger->slug($originalFileName);
                $newFilename = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();
            
                // Move the file to the directory where image are stored
                try {
                    $profileImageFile->move($this->getParameter('profiles_directory'), $newFilename);
                } catch(FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                
                // updates the 'image' property to store the image file name
                // instead of its contents
                $userProfile = $user->getUserProfile() ?? new UserProfile();
                $userProfile->setImage($newFilename);
                $user->setUserProfile($userProfile);
                $entity->persist($user);
                $entity->flush();
                $this->addFlash('success', 'Your profile image was updated.');
                
                return $this->redirectToRoute('app_setting_profile_image');

            }
        }
        return $this->render('setting_profile/profile_image.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
