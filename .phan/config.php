<?php

return [
	'directory_list' => [
		'src',
		'lib',
		'vendor',
	],
	'exclude_analysis_directory_list' => [
		'vendor',
	],
	'suppress_issue_types' => [
		'PhanTypeInvalidBitwiseBinaryOperator',
		'PhanTypeObjectUnsetDeclaredProperty',
		'PhanUndeclaredClassMethod',
		'PhanTypeMismatchProperty',
		'PhanTypeExpectedObjectPropAccessButGotNull',
		'PhanTypeMismatchDimFetchNullable',
		'PhanUndeclaredMethod',
		'PhanTypeMismatchArgument',
		'PhanUndeclaredStaticMethod',
		'PhanUndeclaredConstant',
		'PhanTypeExpectedObjectPropAccess',
		'PhanUndeclaredTypeParameter',
		'PhanUndeclaredProperty',
		'PhanTypeNonVarPassByRef',
		'PhanUnusedGotoLabel',
		'PhanTypeMismatchDimFetch',
		'PhanTypeMismatchDimAssignment',
		'PhanTypeMismatchDimFetch',
		'PhanNonClassMethodCall',
		'PhanUndeclaredTypeReturnType',
		'PhanUndeclaredVariableDim',
		'PhanTypeInvalidDimOffset',
	],
];
