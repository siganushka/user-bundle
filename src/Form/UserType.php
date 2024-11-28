<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Siganushka\UserBundle\Form\Type\UserRoleEntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', UserRoleEntityType::class, [
                'label' => 'user.role',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
            ])
            ->add('identifier', TextType::class, [
                'label' => 'user.identifier',
                'constraints' => new NotBlank(),
            ])
            ->add('plainPassword', RepeatedPasswordType::class, [
                'first_options' => [
                    'label' => 'user.password',
                    'constraints' => new NotBlank(groups: ['NotBlank']),
                ],
                'second_options' => [
                    'label' => 'user.password_confirmation',
                    'constraints' => new NotBlank(groups: ['NotBlank']),
                ],
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        if ($data instanceof ResourceInterface && $data->getId()) {
            // Using dynamic name for RepeatedType
            $plainPassword = $form->get('plainPassword');
            $firstName = $plainPassword->getConfig()->getOption('first_name', 'first');

            $view['plainPassword'][$firstName]->vars['help'] = 'user.password.help';
            $view['plainPassword'][$firstName]->vars['help_attr'] = ['class' => 'text-warning'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => new UniqueEntity(fields: 'identifier'),
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                return $data instanceof ResourceInterface && $data->getId()
                    ? ['Default']
                    : ['Default', 'NotBlank'];
            },
        ]);
    }
}
