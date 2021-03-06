<?php

return array(
    'services' => array(
        'app' => array(
            'class' => 'Symfony\Component\Console\Application',
            'calls' => array(
                array('addCommands', array('#command')),
            ),
        ),
        'builder' => array(
            'class' => 'Syringe\Component\Builder\Builder',
            'calls' => array(
                array('setResolver', ['@resolver']),
                array('addServiceVisitors', ['#service_visitor']),
            ),
        ),
        'resolver' => array(
            'class' => 'Syringe\Component\Builder\ParameterResolver\Resolver',
        ),
        'configuration_validator' => array(
            'class' => 'Syringe\Component\Builder\ServiceVisitor\ConfigurationValidator',
            'arguments' => array(
                array('class', 'factoryMethod', 'factoryStaticMethod', 'scope', 'arguments', 'calls', 'properties', 'preTriggers', 'postTriggers', 'tags', 'alias', 'parent'),
                array('singleton', 'factory', 'prototype', 'synthetic'),
            ),
        ),
        'service_collector' => array(
            'class' => 'Syringe\Component\Builder\ServiceCollector\ServiceCollector',
        ),
        'alias_collector' => array(
            'class' => 'Syringe\Component\Builder\ServiceCollector\AliasCollector',
        ),
        'tag_collector' => array(
            'class' => 'Syringe\Component\Builder\ServiceCollector\TagCollector',
        ),
        'config_parser' => array(
            'class' => 'Syringe\Component\Parser\DelegatedParser',
            'arguments' => array('#parser'),
        ),
        'php_parser' => array(
            'class' => 'Syringe\Component\Parser\PhpParser',
        ),
        'yaml_parser' => array(
            'class' => 'Syringe\Component\Parser\Sf2YamlParser',
        ),
        'build_command' => array(
            'class' => 'Syringe\Command\BuildCommand',
            'arguments' => ['@builder', '@config_parser'],
        ),
    ),
    'tags' => array(
        'command' => array(
            'build_command',
        ),
        'service_visitor' => array(
            'configuration_validator',
            'service_collector',
            'alias_collector',
            'tag_collector',
        ),
        'parser' => array(
            'php_parser',
            'yaml_parser',
        ),
    ),
);
