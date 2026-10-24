<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Siganushka\UserBundle\Entity\AbstractUser;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait UserCommandTrait
{
    #[Required]
    public UserRepository $repository;

    public function getUserByArgument(InputInterface $input): AbstractUser
    {
        $identifier = $input->getArgument('identifier');
        if (!\is_string($identifier)) {
            throw new \InvalidArgumentException('[identifier] The value must be a string.');
        }

        $entity = $this->repository->findOneByIdentifier($identifier)
            ?? throw new \InvalidArgumentException(\sprintf('[identifier] User with value "%s" not found.', $identifier));

        return $entity;
    }
}
