#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
#include "php.h"
#include "php_<?php echo $name; ?>.h"
#include <stdint.h>

ZEND_DECLARE_MODULE_GLOBALS(<?php echo $name; ?>)

PHP_MINFO_FUNCTION(<?php echo $name; ?>) {
    php_info_print_table_start();
    php_info_print_table_row(2, "<?php echo addslashes($name); ?> support", "Enabled");
    php_info_print_table_end();	
}

static PHP_MINIT_FUNCTION(<?php echo $name; ?>) {
<?php
foreach ($stringConstants as $constant) {
    echo "\t{$uppername}_G(string_constants)[" . $constant->idx . "] = zend_string_init(\"" . addslashes($constant->value) . "\", " . strlen($constant->value) . ", 1);\n";
}
?>
}

static PHP_MSHUTDOWN_FUNCTION(<?php echo $name; ?>) {
<?php
foreach ($stringConstants as $constant) {
    echo "\tzend_string_release({$uppername}_G(string_constants)[" . $constant->idx . "]);\n";
}
?>
}

<?php

echo implode("\n", $functionHeaders);

echo "\n\n";

echo implode("\n", $functions);

echo "\n\n";

echo implode("\n", $argInfo);

echo "\n\n";
?>

zend_function_entry <?php echo $name; ?>_functions[] = {
	<?php echo implode("\n\t", $functionEntry); ?>

	{NULL, NULL, NULL};
};

zend_module_entry <?php echo $name; ?>_module_entry = {
	STANDARD_MODULE_HEADER,
	PHP_<?php echo $uppername; ?>_EXTNAME,
	<?php echo $name; ?>_functions,
	PHP_MINIT(<?php echo $name; ?>),
	PHP_MSHUTDOWN(<?php echo $name; ?>),
	NULL,
	NULL,
	PHP_MINFO(<?php echo $name; ?>),
	PHP_<?php echo $uppername; ?>_VERSION,
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_<?php echo $uppername; ?>

ZEND_GET_MODULE(<?php echo $name; ?>)
#endif
