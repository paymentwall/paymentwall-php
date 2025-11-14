<?php

$finder = PhpCsFixer\Finder::create()
    ->path('features')
    ->path('lib')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'function_declaration' => false,
    'trailing_comma_in_multiline' => true,
    'no_unused_imports' => true,
    'declare_strict_types' => false,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setUsingCache(false)
    ->setFinder($finder)
;
