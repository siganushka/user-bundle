<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Identifier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class IdentifierType extends AbstractType implements IdentifierTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new Length(min: 4, max: 32);

            return $constraints;
        });
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
