#!/bin/bash

vendor/bin/php-cs-fixer fix --allow-risky=yes
vendor/bin/phpunit
