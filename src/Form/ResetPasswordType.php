<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rawPassword', RepeatedPasswordType::class, [
                'label' => 'New Password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('validation_groups', ['Default', 'PasswordRequired']);
    }
}
