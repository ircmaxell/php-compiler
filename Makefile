#!make

include .env
export $(shell sed 's/=.*//' .env)

.PHONY: benchmark
benchmark: rebuild-changed
	$(PHP) script/bench.php

.PHONY: build
build: rebuild fix test rebuild-examples

.PHONY: test
test: rebuild-changed
	$(PHP) vendor/bin/phpunit

.PHONY: rebuild
rebuild:
	$(PHP) script/rebuild.php

.PHONY: rebuild-changed
rebuild-changed:
	$(PHP) script/rebuild.php onlyChanged

.PHONY: rebuild-examples
rebuild-examples:
	$(PHP) script/rebuild-examples.php

.PHONY: fix
fix:
	$(PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes