<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form\Type;

use Siganushka\UserBundle\Identifier\IdentifierTypeInterface;
use Symfony\Component\Form\AbstractType;

class UserIdentifierType extends AbstractType
{
    public function __construct(private readonly IdentifierTypeInterface $identifierType)
    {
    }

    public function getParent(): string
    {
        return $this->identifierType::class;
    }
}
