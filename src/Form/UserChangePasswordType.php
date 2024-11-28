<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => new UserPassword(),
            ])
            ->add('newPassword', RepeatedPasswordType::class, [
                'first_options' => [
                    'label' => 'New Password',
                    'constraints' => new NotBlank(),
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'constraints' => new NotBlank(),
                ],
            ])
        ;
    }
}
