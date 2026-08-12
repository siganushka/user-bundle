<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\DependencyInjection;

use Siganushka\UserBundle\Entity\AbstractUser;
use Siganushka\UserBundle\Entity\AbstractUserLogin;
use Siganushka\UserBundle\Identifier\IdentifierType;
use Siganushka\UserBundle\Identifier\IdentifierTypeInterface;
use Siganushka\UserBundle\Repository\UserLoginRepository;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class Configuration implements ConfigurationInterface
{
    public const RESOURCE_MAPPING = [
        'user_class' => [AbstractUser::class, UserRepository::class],
        'user_login_class' => [AbstractUserLogin::class, UserLoginRepository::class],
    ];

    /**
     * @return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_user');
        $rootNode = $treeBuilder->getRootNode();

        foreach (self::RESOURCE_MAPPING as $configName => [$interface]) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $interface, true))
                        ->thenInvalid('The value must be instanceof '.$interface.', %s given.')
                    ->end()
                ->end()
            ;
        }

        $rootNode->children()
            ->scalarNode('identifier_type')
                ->cannotBeEmpty()
                ->defaultValue(IdentifierType::class)
                ->validate()
                    ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, IdentifierTypeInterface::class, true))
                    ->thenInvalid('The value must be instanceof '.IdentifierTypeInterface::class.', %s given.')
                ->end()
            ->end()
            ->enumNode('password_strength_min_score')
                ->values([
                    -1,
                    PasswordStrength::STRENGTH_WEAK,
                    PasswordStrength::STRENGTH_MEDIUM,
                    PasswordStrength::STRENGTH_STRONG,
                    PasswordStrength::STRENGTH_VERY_STRONG,
                ])
                ->defaultValue(PasswordStrength::STRENGTH_WEAK)
            ->end()
        ;

        return $treeBuilder;
    }
}
