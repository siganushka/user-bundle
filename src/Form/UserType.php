<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'generic.enable',
                'required' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return RegistrationType::class;
    }
}
