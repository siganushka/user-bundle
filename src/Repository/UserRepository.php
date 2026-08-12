<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Repository;

use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\UserBundle\Entity\AbstractUser;

/**
 * @template T of AbstractUser = AbstractUser
 *
 * @extends GenericEntityRepository<T>
 */
class UserRepository extends GenericEntityRepository
{
    /**
     * @return T|null
     */
    public function findOneByIdentifier(string $identifier): ?AbstractUser
    {
        return $this->findOneBy(compact('identifier'));
    }
}
