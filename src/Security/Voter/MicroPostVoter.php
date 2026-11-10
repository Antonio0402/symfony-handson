<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class MicroPostVoter extends Voter
{
    public function __construct(
        private Security $security
    ) {
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [MicroPost::EDIT, MicroPost::VIEW])
            && $subject instanceof MicroPost;
    }
    /**
     * 
     *
     * @param string $attribute
     * @param MicroPost $subject
     * @param TokenInterface $token
     * @param Vote|null $vote
     * @return boolean
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {   
        /**
         * @var User $user
         */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        // if (!$user instanceof UserInterface) {
        //     $vote?->addReason('The user must be logged in to access this resource.');

        //     return false;
        // }

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        $isAuth = $user instanceof UserInterface;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case MicroPost::EDIT:
               return $isAuth && (
                    ($subject->getAuthor()->getId() === $user->getId()) 
                    || $this->security->isGranted('ROLE_EDITOR')
                );
            case MicroPost::VIEW:
                return false;
        }

        return false;
    }
}
