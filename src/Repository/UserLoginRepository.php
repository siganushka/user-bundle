<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\UserBundle\Entity\AbstractUserLogin;

/**
 * @template T of AbstractUserLogin = AbstractUserLogin
 *
 * @extends GenericEntityRepository<T>
 */
class UserLoginRepository extends GenericEntityRepository
{
}
