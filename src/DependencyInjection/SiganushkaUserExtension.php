<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\DependencyInjection;

use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Siganushka\UserBundle\Identifier\IdentifierTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SiganushkaUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach (Configuration::RESOURCE_MAPPING as $configName => [, $repositoryClass]) {
            $repositoryClass = $container->findDefinition($repositoryClass);
            $repositoryClass->setArgument('$entityClass', $config[$configName]);
        }

        $repeatedPasswordType = $container->findDefinition(RepeatedPasswordType::class);
        $repeatedPasswordType->setArgument('$passwordStrengthMinScore', $config['password_strength_min_score']);

        $container->setAlias(IdentifierTypeInterface::class, $config['identifier_type']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = array_merge(...$configs);

        $resolveTargetEntities = [];
        foreach (Configuration::RESOURCE_MAPPING as $configName => [$interface]) {
            $resolveTargetEntities[$interface] = $config[$configName] ?? null;
        }

        if (\count($rte = array_filter($resolveTargetEntities))) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => ['resolve_target_entities' => $rte],
            ]);
        }
    }
}
