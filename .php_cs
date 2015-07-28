<?php

$header = <<<'EOF'
This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code

@copyright 2015 Anthony Ferrara. All rights reserved
@license MIT See LICENSE at the root of the project for more info
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'header_comment',
		'linefeed',
		'indentation',
		'elseif',
		'line_after_namespace',
		'lowercase_constants',
		'lowercase_keywords',
		'method_argument_space',
        'single_blank_line_before_namespace',
        'ordered_use',
		'short_array_syntax',
		'single_line_after_imports',
		'visibility',
		'trailing_spaces',
		'concat_with_spaces',
		'align_double_arrow',
		'unused_use',
		'ternary_spaces',
		'remove_leading_slash_use',
		'remove_lines_between_uses',
		'phpdoc_indent',
		'phpdoc_no_access',
		'phpdoc_params',
		'phpdoc_scalar',
		'phpdoc_separation',
		'phpdoc_trim',
		'phpdoc_var_without_name',
		'phpdoc_order',
		'no_empty_lines_after_phpdocs',
	])
	->finder(
		Symfony\CS\Finder\DefaultFinder::create()
			->in(__DIR__ . "/lib")
//			->in(__DIR__ . "/test")
	)
;
