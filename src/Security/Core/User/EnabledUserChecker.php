<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Security\Core\User;

use Siganushka\Contracts\Doctrine\EnableInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EnabledUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof EnableInterface && !$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('Account is disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
