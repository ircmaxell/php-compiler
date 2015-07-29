PHP_ARG_ENABLE(<?php echo $name; ?>, enable <?php echo $name; ?> support,
    [ --enable-<?php echo $name; ?>		Enable <?php echo $name; ?> support], yes)

if test "$PHP_<?php echo $uppername; ?>" != "no"; then
	AC_MSG_CHECKING([Checking for supported PHP versions])
	PHP_<?php echo $uppername; ?>_FOUND_VERSION=`${PHP_CONFIG} --version`
	PHP_<?php echo $uppername; ?>_FOUND_VERNUM=`echo "${PHP_<?php echo $uppername; ?>_FOUND_VERSION}" | $AWK 'BEGIN {fs = "."; } { printf "%d", ([$]1 * 100 + [$]2) * 100 + [$]3;}'`
	if test "$PHP_<?php echo $uppername; ?>_FOUND_VERNUM" -lt "70000"; then
		AC_MSG_ERROR([not supported. Need a PHP version >= 7.0.0 (found $PHP_<?php echo $uppername; ?>_FOUND_VERSION: $PHP_<?php echo $uppername; ?>_FOUND_VERNUM)])
	else
		AC_MSG_RESULT([supported ($PHP_<?php echo $uppername; ?>_FOUND_VERSION)])
	fi
	AC_DEFINE(HAVE_<?php echo $uppername; ?>, 1, [Compile with <?php echo $name; ?> support])
	PHP_NEW_EXTENSION(<?php echo $name;?>, <?php echo $name; ?>.c, $ext_shared)
fi