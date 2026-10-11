<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\UserBundle\Dto\UserQueryDto;
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

    public function createQueryBuilderFromDto(string $alias, UserQueryDto $dto): QueryBuilder
    {
        $criteria = self::createCriteriaFromDto($dto);

        $qb = $this->createQueryBuilderWithOrderBy($alias);
        $qb->addCriteria($criteria);

        return $qb;
    }
}
