<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RepeatedPasswordType extends AbstractType
{
    /**
     * @param PasswordStrength::STRENGTH_*|null $passwordStrengthMinScore
     */
    public function __construct(private readonly ?int $passwordStrengthMinScore = null)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            // @see https://symfony.com/doc/current/reference/constraints/PasswordStrength.html
            'constraints' => new PasswordStrength(minScore: $this->passwordStrengthMinScore),
        ]);
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
