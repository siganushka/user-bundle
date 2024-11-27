<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Event;

use Siganushka\UserBundle\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractUserEvent extends Event
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
