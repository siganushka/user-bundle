<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Input\InputInterface;

trait UserCommandTrait
{
    private readonly UserRepository $repository;

    public function getUserByArgument(InputInterface $input): User
    {
        $identifier = $input->getArgument('identifier');
        if (!\is_string($identifier)) {
            throw new \InvalidArgumentException('[identifier] The value must be a string.');
        }

        $entity = $this->repository->findOneByIdentifier($identifier);
        if (!$entity) {
            throw new \InvalidArgumentException(\sprintf('[identifier] User with value "%s" not found.', $identifier));
        }

        return $entity;
    }
}
