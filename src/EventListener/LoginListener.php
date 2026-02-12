<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[AsEventListener]
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }

    #[AsEventListener]
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $this->logger->debug(__METHOD__);
    }
}
