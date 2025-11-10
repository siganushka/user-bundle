<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Siganushka\UserBundle\Form\Type\UserIdentifierType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', UserIdentifierType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('password', RepeatedPasswordType::class, [
                'constraints' => new NotBlank(groups: ['PasswordRequired']),
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'generic.enabled',
                'required' => false,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        if ($form->has('password') && $data instanceof ResourceInterface && $data->getId()) {
            // Form fields can be added/removed via extensions.
            $password = $form->get('password');
            $first = $password->getConfig()->getOption('first_name', 'first');

            $view['password'][$first]->vars['help'] = 'Please do not fill in if you do not want to change the password.';
            $view['password'][$first]->vars['help_attr'] = ['class' => 'text-warning'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->repository->getClassName(),
            'constraints' => new UniqueEntity('identifier', entityClass: User::class),
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                return $data instanceof ResourceInterface && $data->getId()
                    ? ['Default']
                    : ['Default', 'PasswordRequired'];
            },
        ]);
    }
}
