ARG_ENABLE("<?php echo $name ?>", "for <?php echo $name ?> support", "no");

if (PHP_<?php echo $uppername; ?> != "no") {
	EXTENSION(<?php echo $name; ?>, <?php echo $name; ?>.c, PHP_<?php echo $uppername; ?>_SHARED, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1);
} 
