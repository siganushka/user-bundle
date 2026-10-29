<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Siganushka\UserBundle\EventListener\LoginListener;
use Siganushka\UserBundle\Repository\UserLoginRepository;
use Siganushka\UserBundle\Tests\Fixtures\TestAuthenticator;
use Siganushka\UserBundle\Tests\Fixtures\TestPlainUser;
use Siganushka\UserBundle\Tests\Fixtures\TestUser;
use Siganushka\UserBundle\Tests\Fixtures\TestUserLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListenerTest extends TestCase
{
    private function createUser(): TestUser
    {
        return (new TestUser())->setIdentifier('user@example.com')->setPassword('hashed-password');
    }

    private function createEvent(UserInterface $user, AuthenticatorInterface $authenticator, Request $request): LoginSuccessEvent
    {
        $passport = $this->createMock(Passport::class);
        $passport->method('getUser')->willReturn($user);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return new LoginSuccessEvent($authenticator, $passport, $token, $request, null, 'main');
    }

    private function createRequest(?string $userAgent = 'PHPUnit', string $clientIp = '10.0.0.1'): Request
    {
        $request = Request::create('/login', 'POST', [], [], [], ['REMOTE_ADDR' => $clientIp]);

        if (null === $userAgent) {
            $request->headers->remove('User-Agent');
        } else {
            $request->headers->set('User-Agent', $userAgent);
        }

        return $request;
    }

    private function createListener(EntityManagerInterface $entityManager, UserLoginRepository $repository): LoginListener
    {
        return new LoginListener($entityManager, $repository);
    }

    public function testIgnoresNonBundleUser(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::never())->method('flush');

        $repository = $this->createMock(UserLoginRepository::class);
        $repository->expects(static::never())->method('createNew');

        $this->createListener($entityManager, $repository)(
            $this->createEvent(new TestPlainUser(), new TestAuthenticator(), $this->createRequest())
        );
    }

    public function testIgnoresNonInteractiveAuthenticator(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::never())->method('flush');

        $repository = $this->createMock(UserLoginRepository::class);
        $repository->expects(static::never())->method('createNew');

        $this->createListener($entityManager, $repository)(
            $this->createEvent($this->createUser(), new TestAuthenticator(false), $this->createRequest())
        );
    }

    public function testCreatesLoginRecordOnInteractiveLogin(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();
        $authenticator = new TestAuthenticator();

        $repository = $this->createMock(UserLoginRepository::class);
        $repository->expects(static::once())
            ->method('createNew')
            ->willReturn($login);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::once())->method('flush');

        $this->createListener($entityManager, $repository)(
            $this->createEvent($user, $authenticator, $this->createRequest('Mozilla/5.0', '10.0.0.1'))
        );

        static::assertSame('10.0.0.1', $login->getClientIp());
        static::assertSame('Mozilla/5.0', $login->getUserAgent());
        static::assertSame(TestAuthenticator::class, $login->getAuthenticator());
        static::assertCount(1, $user->getLogins());
        static::assertSame($user, $login->getUser());
    }

    public function testTruncatesLongUserAgent(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();

        $repository = $this->createMock(UserLoginRepository::class);
        $repository->expects(static::once())
            ->method('createNew')
            ->willReturn($login);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::once())->method('flush');

        $this->createListener($entityManager, $repository)(
            $this->createEvent($user, new TestAuthenticator(), $this->createRequest(str_repeat('a', 300)))
        );

        static::assertSame(255, \strlen((string) $login->getUserAgent()));
        static::assertSame(str_repeat('a', 255), $login->getUserAgent());
    }

    public function testMissingUserAgentIsStoredAsNull(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();

        $repository = $this->createMock(UserLoginRepository::class);
        $repository->method('createNew')->willReturn($login);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(static::once())->method('flush');

        $this->createListener($entityManager, $repository)(
            $this->createEvent($user, new TestAuthenticator(), $this->createRequest(null))
        );

        static::assertNull($login->getUserAgent());
    }
}
