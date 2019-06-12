<?php

namespace Aropixel\ContactBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AropixelContactExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Once the services definition are read, get your service and add a method call to setConfig()
        $sillyServiceDefintion = $container->getDefinition( 'Aropixel\ContactBundle\Services\Sender' );
        $sillyServiceDefintion->addMethodCall( 'setTemplate', array( $config[ 'mail_template' ] ) );
        $sillyServiceDefintion->addMethodCall( 'setTemplateNotify', array( $config[ 'mail_template_notification' ] ) );
        $sillyServiceDefintion->addMethodCall( 'setSenderEmail', array( $config[ 'sender_email' ] ) );
        $sillyServiceDefintion->addMethodCall( 'setSenderName', array( $config[ 'sender_name' ] ) );

    }
}
