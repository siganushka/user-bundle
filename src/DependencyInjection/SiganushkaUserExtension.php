<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Siganushka\UserBundle\Doctrine\HashPasswordListener;
use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
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

        foreach (Configuration::$resourceMapping as $configName => [, $repositoryClass]) {
            $repository = $container->findDefinition($repositoryClass);
            $repository->setArgument('$entityClass', $config[$configName]);
        }

        $hashPasswordListener = $container->findDefinition(HashPasswordListener::class);
        $hashPasswordListener->addTag('doctrine.orm.entity_listener', ['event' => Events::prePersist, 'entity' => $config['user_class']]);
        $hashPasswordListener->addTag('doctrine.orm.entity_listener', ['event' => Events::preUpdate, 'entity' => $config['user_class']]);

        $repeatedPasswordType = $container->findDefinition(RepeatedPasswordType::class);
        $repeatedPasswordType->setArgument('$passwordStrengthMinScore', $config['password_strength_min_score']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $mappingOverride = [];
        foreach (Configuration::$resourceMapping as $configName => [$entityClass]) {
            if ($config[$configName] !== $entityClass) {
                $mappingOverride[$entityClass] = $config[$configName];
            }
        }

        $container->prependExtensionConfig('siganushka_generic', [
            'doctrine' => ['mapping_override' => $mappingOverride],
        ]);
    }
}
