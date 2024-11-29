<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Doctrine;

use Siganushka\UserBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordListener
{
    /**
     * The UserPasswordHasherInterface is provided by "symfony/security-bundle" and is optional.
     */
    public function __construct(private readonly ?UserPasswordHasherInterface $passwordHasher = null)
    {
    }

    public function __invoke(User $entity): void
    {
        $password = $entity->getRawPassword();
        if (!$password) {
            return;
        }

        // If "symfony/security-bundle" is not installed, the password is stored in plaintext.
        if ($this->passwordHasher) {
            $password = $this->passwordHasher->hashPassword($entity, $password);
        }

        $entity->setPassword($password);
    }
}
