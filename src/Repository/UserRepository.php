<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\UserBundle\Entity\User;

/**
 * @extends GenericEntityRepository<User>
 */
class UserRepository extends GenericEntityRepository
{
    public function findOneByIdentifier(string $identifier): ?User
    {
        return $this->findOneBy(compact('identifier'));
    }
}
