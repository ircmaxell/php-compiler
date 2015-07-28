#ifndef PHP_<?php echo $uppername; ?>_H

#define PHP_<?php echo $uppername; ?>_H 1
#define PHP_<?php echo $uppername; ?>_VERSION "1.0"
#define PHP_<?php echo $uppername; ?>_EXTNAME "<?php echo $name ?>"

#include <stdint.h>

#ifdef ZTS
#include "TSRM.h"
#endif

ZEND_BEGIN_MODULE_GLOBALS(<?php echo $name ?>)
    zend_string* string_constants[<?php echo count($stringConstants); ?>];
ZEND_END_MODULE_GLOBALS(<?php echo $name ?>)

#ifdef ZTS
#define <?php echo $uppername; ?>_G(v) TSRM(<?php echo $name; ?>_globals_id, zend_<?php echo $name; ?>_globals *, v)
#else
#define <?php echo $uppername; ?>_G(v) (<?php echo $name; ?>_globals.v)
#endif

extern zend_module_entry <?php echo $name; ?>_module_entry;

#define phpext_<?php echo $name; ?>_ptr &<?php echo $name; ?>_module_entry

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINFO_FUNCTION(<?php echo $name; ?>);

#endif;