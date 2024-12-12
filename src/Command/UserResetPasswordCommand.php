<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Form\ResetPasswordType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;

#[AsCommand('siganushka:user:reset-password', 'Resetting user password.')]
class UserResetPasswordCommand extends Command
{
    use UserCommandTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface $formFactory,
        private readonly UserRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'User unique identifier to be reset.')
            ->addArgument('password', InputArgument::REQUIRED, 'New password to reset.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entity = $this->getUserByArgument($input);

        $first = $second = $input->getArgument('password');
        $newPassword = compact('first', 'second');

        $form = $this->formFactory->create(ResetPasswordType::class, $entity, ['csrf_protection' => false]);
        $form->submit(compact('newPassword'));

        if (!$form->isValid()) {
            $error = $form->getErrors(true, true)->current();

            $field = $error->getOrigin()?->getName() ?? $form->getName();
            if (\in_array($field, ['first', 'second'])) {
                $field = 'password';
            }

            throw new \InvalidArgumentException(\sprintf('[%s] %s', $field, $error->getMessage()));
        }

        $io = new SymfonyStyle($input, $output);
        if (!$io->confirm(\sprintf('Are you sure you want to reset password for user "%s"?', $entity->getUserIdentifier()), false)) {
            return Command::SUCCESS;
        }

        $this->entityManager->flush();

        $io->success(\sprintf('User "%s" password has been reset successfully!', $entity->getUserIdentifier()));

        return Command::SUCCESS;
    }
}
