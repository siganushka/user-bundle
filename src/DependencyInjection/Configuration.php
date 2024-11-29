<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\DependencyInjection;

use Siganushka\UserBundle\Entity\User;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class Configuration implements ConfigurationInterface
{
    public static array $resourceMapping = [
        'user_class' => [User::class, UserRepository::class],
    ];

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_user');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        foreach (static::$resourceMapping as $configName => [$entityClass]) {
            $rootNode->children()
                ->scalarNode($configName)
                    ->defaultValue($entityClass)
                    ->validate()
                        ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !is_subclass_of($v, $entityClass, true))
                        ->thenInvalid('The value must be instanceof '.$entityClass.', %s given.')
                    ->end()
                ->end()
            ;
        }

        $rootNode->children()
            ->enumNode('password_strength_min_score')
                ->values([
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
