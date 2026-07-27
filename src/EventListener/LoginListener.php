<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Entity\UserLogin;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener]
class LoginListener
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $authenticator = $event->getAuthenticator();
        if (!($authenticator instanceof InteractiveAuthenticatorInterface && $authenticator->isInteractive())) {
            return;
        }

        $request = $event->getRequest();
        $ua = $request->headers->get('User-Agent');
        if (\is_string($ua)) {
            $ua = mb_strcut($ua, 0, 255);
        }

        $login = new UserLogin();
        $login->setClientIp($request->getClientIp());
        $login->setUserAgent($ua);
        $login->setAuthenticator($authenticator::class);
        $user->addLogin($login);

        $this->entityManager->flush();
    }
}
