<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\Options;
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
            'label' => 'Password',
            'type' => PasswordType::class,
            'mapped' => false,
        ]);

        $resolver->setNormalizer('first_options', fn (Options $options, array $value) => $value + ['label' => $options['label'], 'hash_property_path' => 'password']);
        $resolver->setNormalizer('second_options', fn (Options $options, array $value) => $value + ['label' => \sprintf('Confirm %s', $options['first_options']['label'])]);

        // @see https://symfony.com/doc/current/reference/constraints/PasswordStrength.html
        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new PasswordStrength(minScore: $this->passwordStrengthMinScore);

            return $constraints;
        });
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
