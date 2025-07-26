<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Identifier;

use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneNumberIdentifierType extends AbstractType implements IdentifierTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('html5', true);
        // @see https://symfony.com/doc/current/reference/forms/types/number.html#input
        $resolver->setDefault('input', 'string');

        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new PhoneNumber();

            return $constraints;
        });
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
