<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Tests\Identifier;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Validator\Constraints\PhoneNumber;
use Siganushka\UserBundle\Identifier\EmailIdentifierType;
use Siganushka\UserBundle\Identifier\IdentifierType;
use Siganushka\UserBundle\Identifier\PhoneNumberIdentifierType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class IdentifierTypeTest extends TestCase
{
    /**
     * @param class-string<AbstractType> $typeClass
     * @param list<Constraint>           $constraints
     *
     * @return array<string, mixed>
     */
    private function configure(string $typeClass, array $constraints = []): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefault('constraints', []);

        $type = new $typeClass();
        $type->configureOptions($resolver);

        return $resolver->resolve(['constraints' => $constraints]);
    }

    /**
     * @param class-string<AbstractType> $typeClass
     * @param list<Constraint>           $constraints
     *
     * @return array<mixed>
     */
    private function resolveConstraints(string $typeClass, array $constraints = []): array
    {
        $options = $this->configure($typeClass, $constraints);
        $resolved = $options['constraints'];

        return \is_array($resolved) ? $resolved : [];
    }

    public function testEmailIdentifierTypeAppendsLengthAndEmailConstraints(): void
    {
        $constraints = $this->resolveConstraints(EmailIdentifierType::class);

        static::assertCount(2, $constraints);

        $length = $constraints[0];
        static::assertInstanceOf(Length::class, $length);
        static::assertSame(64, $length->max);

        static::assertInstanceOf(Email::class, $constraints[1]);
    }

    public function testEmailIdentifierTypeKeepsGivenConstraints(): void
    {
        $given = new Email();
        $constraints = $this->resolveConstraints(EmailIdentifierType::class, [$given]);

        static::assertCount(3, $constraints);
        static::assertSame($given, $constraints[0]);
    }

    public function testIdentifierTypeAppendsLengthConstraints(): void
    {
        $constraints = $this->resolveConstraints(IdentifierType::class);

        static::assertCount(1, $constraints);

        $length = $constraints[0];
        static::assertInstanceOf(Length::class, $length);
        static::assertSame(4, $length->min);
        static::assertSame(32, $length->max);
    }

    public function testPhoneNumberIdentifierTypeAppendsPhoneNumberConstraint(): void
    {
        $options = $this->configure(PhoneNumberIdentifierType::class);

        static::assertTrue($options['html5']);
        static::assertSame('string', $options['input']);

        $constraints = $this->resolveConstraints(PhoneNumberIdentifierType::class);
        static::assertCount(1, $constraints);
        static::assertInstanceOf(PhoneNumber::class, $constraints[0]);
    }

    public function testParents(): void
    {
        static::assertSame(EmailType::class, (new EmailIdentifierType())->getParent());
        static::assertSame(TextType::class, (new IdentifierType())->getParent());
        static::assertSame(NumberType::class, (new PhoneNumberIdentifierType())->getParent());
    }
}
