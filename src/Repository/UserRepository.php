<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\UserBundle\Entity\User;

/**
 * @template T of User = User
 *
 * @extends GenericEntityRepository<T>
 */
class UserRepository extends GenericEntityRepository
{
    /**
     * @return T|null
     */
    public function findOneByIdentifier(string $identifier): ?User
    {
        return $this->findOneBy(compact('identifier'));
    }
}
