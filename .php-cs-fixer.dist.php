<?php

require_once __DIR__ . '/vendor/autoload.php';


$finder = (new PhpCsFixer\Finder())
  ->in(__DIR__)
  ->exclude('var')
  ->exclude('node_modules')
  ->exclude('vendor');

return (new PhpCsFixer\Config())
  ->setLineEnding("\n")
  ->setIndent('  ')
  ->setFinder($finder)
  ->registerCustomFixers(new SymplifyCsFixer\SymplifyCsFixers())
  ->setUsingCache(false)
  ->setRules([
    '@PhpCsFixer' => true,
    '@PHP80Migration' => true,
    '@DoctrineAnnotation' => true,
    SymplifyCsFixer\LineLengthFixer::NAME => [
      'line_length' => 100,
      'break_long_lines' => true,
      'inline_short_lines' => true,
    ],
    SymplifyCsFixer\RemoveUselessDefaultCommentFixer::NAME => true,
    SymplifyCsFixer\DocBlockLineLengthFixer::NAME => true,
    'doctrine_annotation_indentation' => false,
    'multiline_whitespace_before_semicolons' => false,
    'class_attributes_separation' => [
      'elements' => [
        'const' => 'one',
        'method' => 'one',
        'property' => 'one',
        'trait_import' => 'one',
      ],
    ],
    'braces' => ['allow_single_line_closure' => true, 'position_after_functions_and_oop_constructs' => 'same'],
    'concat_space' => ['spacing' => 'one'],
    'no_blank_lines_after_class_opening' => false,
    'no_extra_blank_lines' => [
      'tokens' => ['curly_brace_block', 'throw', 'use'],
    ],
    'phpdoc_summary' => false,
    'self_accessor' => true,
  ]);
