<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

#[AsCommand('siganushka:user:add', 'Add a new User.')]
class UserAddCommand extends Command
{
    private readonly User $entity;
    private readonly FormInterface $form;

    public function __construct(private readonly EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserRepository $repository)
    {
        $this->entity = $repository->createNew();
        $this->form = $formFactory->create(UserType::class, $this->entity, ['csrf_protection' => false]);

        parent::__construct();
    }

    protected function configure(): void
    {
        foreach ($this->form->createView() as $key => $_) {
            /** @var string */
            $name = 'rawPassword' === $key ? 'password' : $key;
            $this->addOption($name, null, InputOption::VALUE_REQUIRED, \sprintf('The %s for user.', $key));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fields = [];
        foreach ($this->form as $key => $_) {
            $fields[] = 'rawPassword' === $key ? 'password' : $key;
        }

        $data = array_intersect_key($input->getOptions(), array_flip($fields));

        $first = $second = $data['password'];
        $data['rawPassword'] = compact('first', 'second');

        unset($data['password']);

        $this->form->submit($data);
        if (!$this->form->isValid()) {
            /** @var string $key */
            foreach ($this->form->createView() as $key => $_) {
                $errors = $this->form->get($key)->getErrors(true, true);
                if (!$errors->count()) {
                    continue;
                }

                $error = $errors->current();

                $field = $error->getOrigin()?->getName() ?? $this->form->getName();
                if (\in_array($field, ['first', 'second'])) {
                    $field = 'password';
                }

                throw new \InvalidArgumentException(\sprintf('[%s] %s', $field, $error->getMessage()));
            }
        }

        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success(\sprintf('The user "%s" has been added successfully!', $this->entity->getIdentifier()));

        return Command::SUCCESS;
    }
}
