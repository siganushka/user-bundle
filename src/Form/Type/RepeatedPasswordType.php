<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
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
        // @see https://symfony.com/doc/current/reference/constraints/PasswordStrength.html
        $strength = new PasswordStrength(minScore: $this->passwordStrengthMinScore);
        $constraints = new NotBlank(groups: ['PasswordRequired']);

        $resolver->setDefaults([
            'label' => 'Password',
            'type' => PasswordType::class,
            'options' => compact('constraints'),
            'constraints' => $strength,
        ]);

        $resolver->setNormalizer('first_options', fn (Options $options, array $value) => $value + ['label' => $options['label']]);
        $resolver->setNormalizer('second_options', fn (Options $options, array $value) => $value + ['label' => \sprintf('Confirm %s', $options['first_options']['label'])]);
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
