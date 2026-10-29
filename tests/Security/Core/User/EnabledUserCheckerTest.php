<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Security\Core\User;

use PHPUnit\Framework\TestCase;
use Siganushka\UserBundle\Security\Core\User\EnabledUserChecker;
use Siganushka\UserBundle\Tests\Fixtures\TestPlainUser;
use Siganushka\UserBundle\Tests\Fixtures\TestUser;
use Symfony\Component\Security\Core\Exception\DisabledException;

class EnabledUserCheckerTest extends TestCase
{
    private function createUser(bool $enabled): TestUser
    {
        $user = new TestUser();

        $user->setIdentifier('user@example.com');
        $user->setEnabled($enabled);

        return $user;
    }

    public function testDoesNothingWhenUserIsEnabled(): void
    {
        $user = $this->createUser(true);

        $checker = new EnabledUserChecker();
        $checker->checkPreAuth($user);

        static::assertTrue($user->isEnabled());
    }

    public function testThrowsForDisabledUser(): void
    {
        $checker = new EnabledUserChecker();
        $user = $this->createUser(false);

        try {
            $checker->checkPreAuth($user);
        } catch (DisabledException $e) {
            static::assertSame($user, $e->getUser());

            return;
        }

        static::fail('A DisabledException was expected.');
    }

    public function testDoesNothingWhenUserIsNotEnableAware(): void
    {
        $this->expectNotToPerformAssertions();

        $checker = new EnabledUserChecker();
        $checker->checkPreAuth(new TestPlainUser());
    }

    public function testCheckPostAuthDoesNothing(): void
    {
        $this->expectNotToPerformAssertions();

        $checker = new EnabledUserChecker();
        $checker->checkPostAuth($this->createUser(false));
    }
}
