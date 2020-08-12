<?php

namespace Aropixel\ContactBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('aropixel_contact');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
             ->children()
                 ->scalarNode('mail_template')
                    ->defaultValue('@AropixelContact/Mail/contact.html.twig')
                 ->end()
                 ->scalarNode('mail_template_notification')
                    ->defaultValue('@AropixelContact/Mail/autoreply.html.twig')
                 ->end()
                 ->scalarNode('attachment_directory')
                    ->defaultValue('private/contact_bundle/attachments')
                 ->end()
                 ->scalarNode('sender_email')
                    ->isRequired()
                 ->end()
                 ->scalarNode('sender_name')
                    ->isRequired()
                 ->end()
             ->end()
         ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
