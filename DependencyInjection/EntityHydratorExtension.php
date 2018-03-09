<?php
/**
 * Created by PhpStorm.
 * User: bosske1
 * Date: 9.3.18.
 * Time: 23.23
 */

namespace Xcentric\EntityHydratorBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EntityHydratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__  . '/../Resources/config')
        );

        $loader->load('services.yml');
    }
}