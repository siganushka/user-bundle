<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('siganushka:user:delete', 'Delete a User.')]
class UserDeleteCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('identifier', null, InputOption::VALUE_REQUIRED, 'The user unique identifier.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null */
        $identifier = $input->getOption('identifier');
        if (!$identifier) {
            throw new \InvalidArgumentException('The option --identifier cannot be empty.');
        }

        $entity = $this->repository->findOneByIdentifier($identifier);
        if (!$entity) {
            throw new \InvalidArgumentException(\sprintf('The user "%s" not found.', $identifier));
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (\Throwable $th) {
            throw new \RuntimeException(\sprintf('Unable to delete user "%s" (%s).', $identifier, $th->getMessage()));
        }

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('The user "%s" has been deleted successfully!', $identifier));

        return Command::SUCCESS;
    }
}
