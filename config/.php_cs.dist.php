<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/../src')
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
        'strict_param' => true,
        'strict_comparison' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_to_comment' => false,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder);
