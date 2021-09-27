<?php


$finder = (new PhpCsFixer\Finder())
	->in(__DIR__)
	->exclude('var')
	->exclude('node_modules')
	->exclude('vendor');

return (new PhpCsFixer\Config())
	->setIndent("\t")
	->setLineEnding("\n")
	// ->setFinder($finder)
	->setRules([
		'@PSR2' => true,
		'@PhpCsFixer' => true,
		'@PHP80Migration' => true,
		'@DoctrineAnnotation' => true,
		'doctrine_annotation_indentation' => false,
		'@Symfony' => true,
		'indentation_type' => true,
		'array_indentation' => true,
		'array_syntax' => ['syntax' => 'short'],
		'combine_consecutive_unsets' => true,
		'multiline_whitespace_before_semicolons' => false,
		'class_attributes_separation' => [
			'elements' => [
				'const' => 'one',
				'method' => 'one',
				'property' => 'one',
				'trait_import' => 'one',
			],
		],
		'single_quote' => true,
		'binary_operator_spaces' => [
			'operators' => [
				// '=>' => 'align',
				// '=' => 'align'
			],
		],
		'braces' => [
			'allow_single_line_closure' => true,
			'position_after_functions_and_oop_constructs' => 'same',
		],
		// 'cast_spaces' => true,
		// 'class_definition' => array('singleLine' => true),
		'concat_space' => ['spacing' => 'one'],
		'declare_equal_normalize' => true,
		'function_typehint_space' => true,
		'include' => true,
		'lowercase_cast' => true,
		// 'native_function_casing' => true,
		// 'new_with_braces' => true,
		'no_blank_lines_after_class_opening' => false,
		// 'no_blank_lines_after_phpdoc' => true,
		// 'no_blank_lines_before_namespace' => true,
		'no_empty_comment' => true,
		// 'no_empty_phpdoc' => true,
		// 'no_empty_statement' => true,
		'no_extra_blank_lines' => [
			'tokens' => [
				'curly_brace_block',
				// 'extra',
				// 'parenthesis_brace_block',
				// 'square_brace_block',
				'throw',
				'use',
			],
		],
		// 'no_leading_import_slash' => true,
		// 'no_leading_namespace_whitespace' => true,
		// 'no_mixed_echo_print' => array('use' => 'echo'),
		'no_multiline_whitespace_around_double_arrow' => true,
		// 'no_short_bool_cast' => true,
		// 'no_singleline_whitespace_before_semicolons' => true,
		'no_spaces_around_offset' => true,
		// 'no_trailing_comma_in_list_call' => true,
		'no_trailing_comma_in_singleline_array' => true,
		// 'no_unneeded_control_parentheses' => true,
		// 'no_unused_imports' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'normalize_index_brace' => true,
		'object_operator_without_whitespace' => true,
		'phpdoc_summary' => false,
		// 'php_unit_fqcn_annotation' => true,
		'return_type_declaration' => true,
		// 'self_accessor' => true,
		// 'short_scalar_cast' => true,
		// 'single_blank_line_before_namespace' => true,
		// 'single_class_element_per_statement' => true,
		// 'standardize_not_equals' => true,
		'ternary_operator_spaces' => true,
		// 'trailing_comma_in_multiline_array' => true,
		'trim_array_spaces' => true,
		'unary_operator_spaces' => true,
		'whitespace_after_comma_in_array' => true,
		'space_after_semicolon' => true,
		// 'single_blank_line_at_eof' => false,
	]);
