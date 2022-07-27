<?php
// require __DIR__ . 'vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/build',
        __DIR__ . '/src',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRules([
        '@PSR12'                    => true,
        'array_syntax'              => ['syntax' => 'short'],
        'array_indentation'         => true,
        'ordered_imports'           => ['sort_algorithm' => 'alpha'],
        'single_line_after_imports' => true,

        /**
         * This removes imports from the same namespace. Do not enable.
         * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/4243
         */
        'no_unused_imports' => false,

        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar'               => true,
        'unary_operator_spaces'       => true,
        'binary_operator_spaces'      => [
            'default'   => 'single_space',
            'operators' => [
                '='  => 'align',
                '=>' => 'align',
            ],
        ],
        'not_operator_with_space'           => false,
        'not_operator_with_successor_space' => false,
        'blank_line_before_statement'       => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ],
        ],
        'no_extra_blank_lines'            => ['tokens' => ['extra']],
        'phpdoc_single_line_var_spacing'  => true,
        'whitespace_after_comma_in_array' => true,
        'phpdoc_var_without_name'         => true,
        'method_argument_space'           => [
            'on_multiline'                     => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'blank_line_after_namespace'   => true,
        'blank_line_after_opening_tag' => true,
        'constant_case'                => [
            'case' => 'lower',
        ],
        'braces' => [
            'allow_single_line_closure'                   => false,
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'single_quote' => true,
    ])
    ->setFinder($finder);
