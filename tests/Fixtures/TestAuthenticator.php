<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Fixtures;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class TestAuthenticator implements InteractiveAuthenticatorInterface
{
    public function __construct(private readonly bool $interactive = true)
    {
    }

    public function supports(Request $request): ?bool
    {
        return null;
    }

    public function authenticate(Request $request): Passport
    {
        throw new \LogicException('Not used in tests.');
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        throw new \LogicException('Not used in tests.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    public function isInteractive(): bool
    {
        return $this->interactive;
    }
}
