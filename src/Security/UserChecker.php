<?php

namespace App\Security;

use App\Entity\User as AppUser;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Undocumented function
     *
     * @param AppUser $user
     * @return void
     */
    public function checkPreAuth(UserInterface $user): void
    {
       if($user->getBannedUntil() === null) {
            return;
       }
       $now = new DateTime();
       if($user->getBannedUntil() > $now) {
            throw new AccessDeniedException('The user has been banned');
       }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        // if (!$user instanceof AppUser) {
        //     return;
        // }

        // // user account is expired, the user may be notified
        // if ($user->isExpired()) {
        //     throw new AccountExpiredException('...');
        // }

        // if (!\in_array('foo', $token->getRoleNames())) {
        //     throw new AccessDeniedException('...');
        // }
    }
}
