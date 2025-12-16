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

#[AsCommand('siganushka:user:enable', 'Enable a User.')]
class UserEnableCommand extends Command
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
            ->addArgument('identifier', InputArgument::REQUIRED, 'User unique identifier to be enable.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entity = $this->getUserByArgument($input);
        if ($entity->isEnabled()) {
            $io->warning(\sprintf('User "%s" has already been enabled (Nothing was done)!', $entity->getUserIdentifier()));

            return Command::SUCCESS;
        }

        if (!$io->confirm(\sprintf('Are you sure you want to enable user "%s"?', $entity->getUserIdentifier()), false)) {
            return Command::SUCCESS;
        }

        $entity->setEnabled(true);
        $this->entityManager->flush();

        $io->success(\sprintf('User "%s" has been enabled!', $entity->getUserIdentifier()));

        return Command::SUCCESS;
    }
}
