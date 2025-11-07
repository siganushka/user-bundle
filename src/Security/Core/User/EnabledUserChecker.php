<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Security\Core\User;

use Siganushka\Contracts\Doctrine\EnableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EnabledUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof EnableInterface) {
            return;
        }

        if (!$user->isEnabled()) {
            $e = new DisabledException('User account is disabled.');
            $e->setUser($user);

            throw $e;
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
