<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => new UserPassword(),
                'mapped' => false,
            ])
            ->add('rawPassword', RepeatedPasswordType::class, [
                'label' => 'New Password',
                'constraints' => new NotBlank(),
            ])
        ;
    }
}
