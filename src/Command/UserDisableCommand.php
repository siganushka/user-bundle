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

#[AsCommand('siganushka:user:disable', 'Disable a User.')]
class UserDisableCommand extends Command
{
    use UserCommandTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'User unique identifier to be disable.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entity = $this->getUserByArgument($input);
        if (!$entity->isEnabled()) {
            $io->warning(\sprintf('User "%s" has already been disabled (Nothing was done)!', $entity->getUserIdentifier()));

            return Command::SUCCESS;
        }

        if (!$io->confirm(\sprintf('Are you sure you want to disable user "%s"?', $entity->getUserIdentifier()), false)) {
            return Command::SUCCESS;
        }

        $entity->setEnabled(false);
        $this->entityManager->flush();

        $io->success(\sprintf('User "%s" has been disabled!', $entity->getUserIdentifier()));

        return Command::SUCCESS;
    }
}
