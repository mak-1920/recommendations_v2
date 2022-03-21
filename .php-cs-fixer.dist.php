<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['public', 'vendor', 'var'])
    ->notPath('*')
    ->in(__DIR__)
    ;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    '@PHP80Migration' => true,
    'array_push' => true,
    'random_api_migration' => true,
    'phpdoc_to_property_type' => true,
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => false,
    'phpdoc_align' => [
        'align' => 'left',
    ],
    'phpdoc_to_comment' => false,
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'no_superfluous_phpdoc_tags' => false,
])
->setFinder($finder)
;