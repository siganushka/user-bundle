<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;

#[AsCommand('siganushka:user:create', 'Create a new User.')]
class UserCreateCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface $formFactory,
        private readonly UserRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('role', null, InputOption::VALUE_REQUIRED, 'Select user role.')
            ->addOption('identifier', null, InputOption::VALUE_REQUIRED, 'The user unique identifier.')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password to login.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getOption('password');

        $data = array_intersect_key($input->getOptions(), array_flip(['role', 'identifier']));
        $data['plainPassword'] = ['first' => $password, 'second' => $password];

        $entity = $this->repository->createNew();

        $form = $this->formFactory->create(UserType::class, $entity);
        $form->submit($data);

        if (!$form->isValid()) {
            $error = $form->getErrors(true, true)->current();

            $propertyName = $error->getOrigin()?->getName() ?? $form->getName();
            if (\in_array($propertyName, ['first', 'second'])) {
                $propertyName = 'password';
            }

            throw new \InvalidArgumentException(\sprintf('[%s] %s', $propertyName, $error->getMessage()));
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('The user "%s" has been added successfully!', $entity->getIdentifier()));

        return Command::SUCCESS;
    }
}
