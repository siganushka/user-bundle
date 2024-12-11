<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Identifier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class EmailIdentifierType extends AbstractType implements IdentifierTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new Length(max: 64);
            $constraints[] = new Email();

            return $constraints;
        });
    }

    public function getParent(): string
    {
        return EmailType::class;
    }
}
