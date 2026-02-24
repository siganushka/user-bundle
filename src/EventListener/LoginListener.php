<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

#[AsEventListener]
class LoginListener
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        $user->setLastLoginIp($event->getRequest()->getClientIp());
        $user->setLastLoginAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }
}
