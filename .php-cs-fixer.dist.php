<?php

// Reference: http://cs.sensiolabs.org/
// Usage: php-cs-fixer fix app

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['bootstrap', 'storage', 'vendor', 'spec', 'nova', 'ssh', 'public', 'database/migrations'])
    ->name('*.php')
    ->name('_ide_helper')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        'multiline_whitespace_before_semicolons' => false,
        'array_syntax' => ['syntax' => 'short'],
        'simplified_null_return' => false,
        'strict_comparison' => true,
        'strict_param' => false,
        'yoda_style' => false,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'no_extra_blank_lines' => ['tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']],
        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_separation' => false,
        'phpdoc_align' => ['align' => 'left'],
    ])
    ->setFinder($finder);
