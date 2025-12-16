<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('siganushka:user:delete', 'Delete a User.')]
class UserDeleteCommand extends Command
{
    use UserCommandTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'User unique identifier to be delete.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entity = $this->getUserByArgument($input);

        $io = new SymfonyStyle($input, $output);
        if (!$io->confirm(\sprintf('Are you sure you want to completely delete user "%s"?', $entity->getUserIdentifier()), false)) {
            return Command::SUCCESS;
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (\Throwable $th) {
            throw new \RuntimeException(\sprintf('Unable to delete user "%s" (%s).', $entity->getUserIdentifier(), $th->getMessage()));
        }

        $io->success(\sprintf('The user "%s" has been deleted successfully!', $entity->getUserIdentifier()));

        return Command::SUCCESS;
    }
}
