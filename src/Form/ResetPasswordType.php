<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newPassword', RepeatedPasswordType::class, [
                'first_options' => [
                    'constraints' => new NotBlank(),
                ],
                'second_options' => [
                    'constraints' => new NotBlank(),
                ],
            ])
        ;
    }
}
