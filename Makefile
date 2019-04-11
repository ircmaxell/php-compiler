
PHP ?= php

build: rebuild-changed
	$(PHP) vendor/bin/phpunit

rebuild:
	$(PHP) rebuild.php

rebuild-changed:
	$(PHP) rebuild.php onlyChanged

fix:
	PHP_CS_FIXER_IGNORE_ENV=true $(PHP) vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes