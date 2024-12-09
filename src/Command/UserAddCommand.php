<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;

#[AsCommand('siganushka:user:add', 'Add a new User.')]
class UserAddCommand extends Command
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
            ->addArgument('identifier', InputArgument::REQUIRED, 'User unique identifier to add.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password to login.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $first = $second = $input->getArgument('password');

        $data = compact('identifier');
        $data['rawPassword'] = compact('first', 'second');

        $entity = $this->repository->createNew();

        $form = $this->formFactory->create(UserType::class, $entity, ['csrf_protection' => false]);
        $form->submit($data);

        if (!$form->isValid()) {
            $error = $form->getErrors(true, true)->current();

            $field = $error->getOrigin()?->getName() ?? $form->getName();
            if (\in_array($field, ['first', 'second'])) {
                $field = 'password';
            }

            throw new \InvalidArgumentException(\sprintf('[%s] %s', $field, $error->getMessage()));
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('The user "%s" has been added successfully!', $entity->getIdentifier()));

        return Command::SUCCESS;
    }
}
