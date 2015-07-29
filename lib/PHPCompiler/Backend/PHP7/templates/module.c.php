#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
#include "php.h"
#include "ext/standard/info.h"
#include "php_<?php echo $name; ?>.h"
#include <stdint.h>



<?php
foreach ($classEntries as $entry) { ?>

zend_class_entry *php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_ce;
zend_object_handlers php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_handlers;
<?php
foreach ($classEntries as $entry) { ?>
HashTable php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handlers;
<?php
}
?>


typedef struct _php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t {
<?php
    foreach ($entry['properties'] as $prop) {
        echo "\t{$prop['ctype']} p_{$prop['name']};\n";
    }
    ?>
	zend_object std;
} php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t;

static inline php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_fetch_object(zend_object *obj) {
    return (php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *)((char*)(obj) - XtOffsetOf(php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t, std));
}

typedef int(*php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_read_t)(php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *obj, zval *retval);
typedef int(*php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_write_t)(php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *obj, zval *newval);

typedef struct _php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler {
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_read_t read_func;
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_write_t write_func;
} php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler;

static void php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_register_prop_handler(HashTable *prop_handler, char *name, php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_read_t read_func, php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_write_t write_func) {
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler handler;
	handler.read_func = read_func;
	handler.write_func = write_func;
	zend_hash_str_add_mem(prop_handler, name, strlen(name), &handler, sizeof(php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler));
}

static void php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_dtor_prop_handler(zval *zv) {
	free(Z_PTR_P(zv));
}

zval *php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_read_property(zval *object, zval *member, int type, void **cache_slot, zval *rv) {
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *obj = php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_fetch_object(Z_OBJ_P(object));
	zend_string *member_str = zval_get_string(member);
	zval *retval;
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler* handler = NULL;

	handler = zend_hash_find_ptr(&php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handlers, member_str);

	if (handler) {
		int ret = handler->read_func(obj, rv);
		if (ret == SUCCESS) {
			retval = rv;
		} else {
			retval = &EG(uninitialized_zval);
		}
	} else {
		zend_object_handlers *std_handler = zend_get_std_object_handlers();
		retval = std_handler->read_property(object, member, type, cache_slot, rv);
	}
	zend_string_release(member_str);
	return retval;
}

void php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_write_property(zval *object, zval *member, zval *value, void **cache_slot) {
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *obj = php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_fetch_object(Z_OBJ_P(object));
	zend_string *member_str = zval_get_string(member);
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler* handler = NULL;

	handler = zend_hash_find_ptr(&php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handlers, member_str);

	if (handler) {
		handler->write_func(obj, value);
	} else {
		zend_object_handlers *std_handler = zend_get_std_object_handlers();
		std_handler->write_property(object, member, value, cache_slot);
	}
	zend_string_release(member_str);
}

<?php
}
?>



ZEND_DECLARE_MODULE_GLOBALS(<?php echo $name; ?>)

PHP_MINFO_FUNCTION(<?php echo $name; ?>) {
    php_info_print_table_start();
    php_info_print_table_row(2, "<?php echo addslashes($name); ?> support", "Enabled");
    php_info_print_table_end();	
}


static PHP_MSHUTDOWN_FUNCTION(<?php echo $name; ?>) {
<?php
foreach ($stringConstants as $constant) {
    echo "\tzend_string_release({$uppername}_G(string_constants)[" . $constant->idx . "]);\n";
}
?>

    return SUCCESS;
}

static inline void hashtable_release(HashTable* ht) {
	if (!--GC_REFCOUNT(ht)) {
		zend_hash_destroy(ht);
	}
}

<?php

echo implode("\n", $functionHeaders);

echo "\n\n";

echo implode("\n", $functions);

echo "\n\n";

foreach ($classEntries as $entry) {
    echo "static inline zend_object* php_{$name}_{$entry['id']}_create(zend_class_entry *ce) {\n";
?>
	php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *intern = (php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t*) ecalloc(1, sizeof(php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t));

	zend_object_std_init(&intern->std, ce);
	object_properties_init(&intern->std, ce);

	intern->std.handlers = &php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_handlers;
	return &intern->std;
<?php
    echo "}\n\n";

    echo "static HashTable* php_{$name}_{$entry['id']}_debug_info(zval *object, int *is_temp) {\n";
?>
    php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_t *obj = php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_fetch_object(Z_OBJ_P(object));
    HashTable *debug_info,
                *std_props;
    zend_string *string_key;
    php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handler *entry;

    std_props = zend_std_get_properties(object);
    debug_info = zend_array_dup(std_props);
    *is_temp = 1;
    ZEND_HASH_FOREACH_STR_KEY_PTR(&php_<?php echo $name; ?>_<?php echo $entry['id']; ?>_prop_handlers, string_key, entry) {
        zval value;

        if (entry->read_func(obj, &value) == FAILURE || !string_key) {
            continue;
        }

        zend_hash_add(debug_info, string_key, &value);
    } ZEND_HASH_FOREACH_END();

    return debug_info;

<?php
    echo "}\n\n";


    foreach ($entry['properties'] as $prop) {
        echo "int php_{$name}_{$entry['id']}_{$prop['name']}_read(php_{$name}_{$entry['id']}_t *obj, zval *retval) {\n";
        echo "\t" . $prop['typeInfo']['ztypeset']("retval", "obj->p_{$prop['name']}") . ";\n";
        echo "\treturn SUCCESS;\n";
        echo "}\n\n";
        echo "int php_{$name}_{$entry['id']}_{$prop['name']}_write(php_{$name}_{$entry['id']}_t *obj, zval *newval) {\n";
        echo "\tif (Z_TYPE_P(newval) != {$prop['typeInfo']['ztype']}) {\n";
        echo "\t\tzend_throw_error(NULL, \"Parameter is not an {$prop['typeInfo']['stringtype']}\");\n";
        echo "\t}\n";
	if (isset($prop['typeInfo']['ztypedtor'])) {
		echo "\tif (obj->p_{$prop['name']}) {\n";
		echo "\t\t{$prop['typeInfo']['ztypedtor']}(obj->p_{$prop['name']});\n";
		echo "\t}\n";
	}
        echo "\tobj->p_{$prop['name']} = {$prop['typeInfo']['ztypefetch']}(newval);\n";
        echo "\treturn SUCCESS;\n";
        echo "}\n\n";
    }
}

echo "\n\n";

echo implode("\n", $argInfo);

echo "\n\n";
?>

zend_function_entry <?php echo $name; ?>_functions[] = {
	<?php echo implode("\n\t", $functionEntry); ?>
	PHP_FE_END
};

static PHP_MINIT_FUNCTION(<?php echo $name; ?>) {
    zend_class_entry ce;
<?php
foreach ($stringConstants as $constant) {
    echo "\t{$uppername}_G(string_constants)[" . $constant->idx . "] = zend_string_init(\"" . addslashes($constant->value) . "\", " . strlen($constant->value) . ", 1);\n";
}
?>

<?php
foreach ($classEntries as $entry) {
    if (count($entry['methods'])) {
        echo "zend_function_entry *php_{$name}_{$entry['id']}_methods[] = {\n";
        foreach ($entry['methods'] as $method) {
            echo "\tPHP_ME({$entry['name']}, {$method['name']}, php_{$name}_{$entry['id']}_{$method['name']}_arginfo, ZEND_ACC_PUBLIC)\n";
        }
        echo "\tPHP_FE_END\n";
        echo "};\n";
    }
}
?>

<?php
foreach ($classEntries as $entry) {
    if ($entry['ns']) {
        if (count($entry['methods'])) {
		echo "\tINIT_NS_CLASS_ENTRY(ce, \"" . addslashes($entry['ns']) . "\", \"{$entry['name']}\", php_{$name}_{$entry['id']}_methods);\n";
	} else echo "\tINIT_NS_CLASS_ENTRY(ce, \"" . addslashes($entry['ns']) . "\", \"{$entry['name']}\", NULL);\n";
    } else {
        if (count($entry['methods'])) {
            echo "\tINIT_CLASS_ENTRY(ce, \"{$entry['name']}\", php_{$name}_{$entry['id']}_methods);\n";
        } else echo "\tINIT_CLASS_ENTRY(ce, \"{$entry['name']}\", NULL);\n";
    }
    echo "\tphp_{$name}_{$entry['id']}_ce = zend_register_internal_class(&ce);\n";
    echo "\tphp_{$name}_{$entry['id']}_ce->create_object = php_{$name}_{$entry['id']}_create;\n";
    echo "\tmemcpy(&php_{$name}_{$entry['id']}_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));\n";
    echo "\tphp_{$name}_{$entry['id']}_handlers.read_property = php_{$name}_{$entry['id']}_read_property;\n";
    echo "\tphp_{$name}_{$entry['id']}_handlers.write_property = php_{$name}_{$entry['id']}_write_property;\n";
    echo "\tphp_{$name}_{$entry['id']}_handlers.get_debug_info = php_{$name}_{$entry['id']}_debug_info;\n";
    echo "\tphp_{$name}_{$entry['id']}_handlers.offset = XtOffsetOf(php_{$name}_{$entry['id']}_t, std);\n";
    echo "\tzend_hash_init(&php_{$name}_{$entry['id']}_prop_handlers, 0, NULL, php_{$name}_{$entry['id']}_dtor_prop_handler, 1);\n";
    foreach ($entry['properties'] as $prop) {
        echo "\tphp_{$name}_{$entry['id']}_register_prop_handler(&php_{$name}_{$entry['id']}_prop_handlers, \"" . addslashes($prop['name']) . "\", php_{$name}_{$entry['id']}_{$prop['name']}_read, php_{$name}_{$entry['id']}_{$prop['name']}_write);\n";
    }
}
?>
    
    return SUCCESS;
}

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

#ifdef ZTS
		ZEND_TSRMLS_CACHE_DEFINE();
#endif
ZEND_GET_MODULE(<?php echo $name; ?>)
#endif
