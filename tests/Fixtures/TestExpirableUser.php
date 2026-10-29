<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Fixtures;

use Siganushka\Contracts\Doctrine\ExpirableInterface;
use Siganushka\Contracts\Doctrine\ExpirableTrait;
use Siganushka\UserBundle\Entity\AbstractUser;

class TestExpirableUser extends AbstractUser implements ExpirableInterface
{
    use ExpirableTrait;
}
