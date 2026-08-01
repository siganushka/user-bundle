<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Form\UserType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

#[AsCommand('siganushka:user:create', 'Create a new User.')]
class UserCreateCommand extends Command
{
    private FormInterface $form;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface $formFactory)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        /**
         * Using form view to get the sorted form fields.
         *
         * @var string $name
         */
        foreach ($this->createForm()->createView() as $name => $_) {
            $this->addOption($name, null, InputOption::VALUE_REQUIRED, \sprintf('The %s to create user.', $name));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $form = $this->createForm();

        $fields = array_keys($form->all());
        $data = array_intersect_key($input->getOptions(), array_flip($fields));

        // Form fields can be added/removed via extensions.
        if (\array_key_exists('password', $data)) {
            $first = $second = $data['password'];
            $data['password'] = compact('first', 'second');
        }

        $form->submit($data);
        if (!$form->isValid()) {
            // $data is sorted form fields
            foreach ($data as $name => $_) {
                $errors = $form->get($name)->getErrors(true);
                if (!$errors->count()) {
                    continue;
                }

                if (\in_array($name, ['first', 'second'])) {
                    $name = 'password';
                }

                throw new \InvalidArgumentException(\sprintf('[%s] %s', $name, $errors->current()->getMessage()));
            }
        }

        /** @var User */
        $user = $this->form->getData();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('The user "%s" has been created successfully!', $user->getIdentifier()));

        return Command::SUCCESS;
    }

    private function createForm(): FormInterface
    {
        if (isset($this->form)) {
            return $this->form;
        }

        return $this->form = $this->formFactory->create(UserType::class, null, ['csrf_protection' => false]);
    }
}
