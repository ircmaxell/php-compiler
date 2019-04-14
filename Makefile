#!make

.PHONY: composer-install
composer-install:
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php /composer.phar install

.PHONY: shell
shell:
	docker run -it -v $(shell pwd):/compiler php-compiler-16-04 /bin/bash

.PHONY: docker-build
docker-build:
	docker build -t php-compiler-16-04 Docker/ubuntu-16.04

.PHONY: benchmark
benchmark: rebuild-changed
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php script/bench.php

.PHONY: build
build: docker-build composer-install rebuild fix test rebuild-examples

.PHONY: test
test: rebuild-changed
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php vendor/bin/phpunit

.PHONY: rebuild
rebuild:
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php script/rebuild.php

.PHONY: rebuild-changed
rebuild-changed:
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php script/rebuild.php onlyChanged

.PHONY: rebuild-examples
rebuild-examples:
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php script/rebuild-examples.php

.PHONY: fix
fix:
	docker run -v $(shell pwd):/compiler php-compiler-16-04 php vendor/bin/php-cs-fixer fix --allow-risky=yes

