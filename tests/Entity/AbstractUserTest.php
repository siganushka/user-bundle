<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\UserBundle\Tests\Fixtures\TestUser;
use Siganushka\UserBundle\Tests\Fixtures\TestUserLogin;

class AbstractUserTest extends TestCase
{
    private function createUser(?string $identifier = 'user@example.com'): TestUser
    {
        return (new TestUser())->setIdentifier($identifier)->setPassword('hashed-password');
    }

    public function testRolesAreAlwaysRoleUser(): void
    {
        static::assertSame(['ROLE_USER'], $this->createUser()->getRoles());
        static::assertSame(['ROLE_USER'], $this->createUser(null)->getRoles());
    }

    public function testIdentifierIsUsedAsUserIdentifier(): void
    {
        static::assertSame('user@example.com', $this->createUser()->getUserIdentifier());
    }

    public function testUserIdentifierFallsBackToObjectIdWhenIdentifierIsNull(): void
    {
        $user = $this->createUser(null);

        static::assertNull($user->getIdentifier());
        static::assertSame((string) spl_object_id($user), $user->getUserIdentifier());
        static::assertNotSame('', $user->getUserIdentifier());
    }

    public function testSettersAreFluentAndKeepValues(): void
    {
        $user = $this->createUser();

        static::assertSame($user, $user->setIdentifier('another@example.com'));
        static::assertSame($user, $user->setPassword('another-password'));
        static::assertSame('another@example.com', $user->getIdentifier());
        static::assertSame('another-password', $user->getPassword());
    }

    public function testEnabledByDefault(): void
    {
        $user = $this->createUser();

        static::assertTrue($user->isEnabled());
        static::assertSame($user, $user->setEnabled(false));
        static::assertFalse($user->isEnabled());
    }

    public function testAddLoginMaintainsBidirectionalRelation(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();

        static::assertSame($user, $user->addLogin($login));
        static::assertCount(1, $user->getLogins());
        static::assertTrue($user->getLogins()->contains($login));
        static::assertSame($user, $login->getUser());
    }

    public function testAddSameLoginTwiceAddsOnlyOnce(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();

        $user->addLogin($login);
        $user->addLogin($login);

        static::assertCount(1, $user->getLogins());
        static::assertSame($user, $login->getUser());
    }

    public function testRemoveLoginDetachesRelation(): void
    {
        $user = $this->createUser();
        $login = new TestUserLogin();
        $user->addLogin($login);

        static::assertSame($user, $user->removeLogin($login));
        static::assertCount(0, $user->getLogins());
        static::assertFalse($user->getLogins()->contains($login));
        static::assertNull($login->getUser());
    }

    public function testRemoveLoginKeepsRelationOfForeignLogin(): void
    {
        $owner = $this->createUser('owner@example.com');
        $other = $this->createUser('other@example.com');
        $login = new TestUserLogin();
        $other->addLogin($login);

        $owner->removeLogin($login);

        static::assertSame($other, $login->getUser());
    }
}
