<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Security\Core\User;

use Siganushka\Contracts\Doctrine\ExpirableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpiredUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof ExpirableInterface) {
            return;
        }

        $expired = $user->getExpiredAt();
        if ($expired && $expired < new \DateTime()) {
            $e = new AccountExpiredException('User has expired.');
            $e->setUser($user);

            throw $e;
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
