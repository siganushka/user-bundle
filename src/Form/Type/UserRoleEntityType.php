<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form\Type;

use Siganushka\UserBundle\Repository\UserRoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleEntityType extends AbstractType
{
    public function __construct(private readonly UserRoleRepository $repository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->repository->getClassName(),
            'choice_label' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
