<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Fixtures;

use Symfony\Component\Security\Core\User\UserInterface;

class TestPlainUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return 'other@example.com';
    }
}
