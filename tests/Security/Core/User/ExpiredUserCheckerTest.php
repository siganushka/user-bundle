<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Security\Core\User;

use PHPUnit\Framework\TestCase;
use Siganushka\UserBundle\Security\Core\User\ExpiredUserChecker;
use Siganushka\UserBundle\Tests\Fixtures\TestExpirableUser;
use Siganushka\UserBundle\Tests\Fixtures\TestPlainUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;

class ExpiredUserCheckerTest extends TestCase
{
    private function createExpirableUser(?\DateTimeImmutable $expiredAt): TestExpirableUser
    {
        $user = new TestExpirableUser();

        $user->setIdentifier('user@example.com');
        $user->setExpiredAt($expiredAt);

        return $user;
    }

    public function testDoesNothingWhenExpiredAtIsNull(): void
    {
        $user = $this->createExpirableUser(null);

        $checker = new ExpiredUserChecker();
        $checker->checkPreAuth($user);

        static::assertNull($user->getExpiredAt());
    }

    public function testDoesNothingWhenUserIsNotExpired(): void
    {
        $user = $this->createExpirableUser(new \DateTimeImmutable('+1 day'));

        $checker = new ExpiredUserChecker();
        $checker->checkPreAuth($user);

        static::assertNotNull($user->getExpiredAt());
    }

    public function testThrowsForExpiredUser(): void
    {
        $checker = new ExpiredUserChecker();
        $user = $this->createExpirableUser(new \DateTimeImmutable('-1 day'));

        try {
            $checker->checkPreAuth($user);
        } catch (AccountExpiredException $e) {
            static::assertSame($user, $e->getUser());

            return;
        }

        static::fail('An AccountExpiredException was expected.');
    }

    public function testDoesNothingWhenUserIsNotExpirable(): void
    {
        $this->expectNotToPerformAssertions();

        $checker = new ExpiredUserChecker();
        $checker->checkPreAuth(new TestPlainUser());
    }
}
