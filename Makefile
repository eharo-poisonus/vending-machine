CONTAINER_NAME=vending-machine-php
CONTAINER_CMD=docker exec -it $(CONTAINER_NAME)
COMPOSER_CMD=$(CONTAINER_CMD) composer
PHP_CMD=$(CONTAINER_CMD) php
COMPOSER_FILE=docker-compose.yml

### Makefile
help:
	@echo "Available Make targets:"
	@grep -E '^[a-zA-Z_-]+:' Makefile | sed 's/:.*//'

### Composer
composer-install:
	$(COMPOSER_CMD) install --optimize-autoloader --ignore-platform-req=ext-openswoole

composer-update:
	$(COMPOSER_CMD) update

composer-require:
	@read -p "Enter the library to require (e.g. symfony/var-dumper): " library; \
	$(COMPOSER_CMD) require $$library --ignore-platform-req=ext-openswoole

composer-dump-autoload:
	$(COMPOSER_CMD) dump-autoload

### Docker
shell:
	$(CONTAINER_CMD) /bin/bash

build:
	docker-compose up -d --build
	@$(MAKE) composer-install

clean:
	docker-compose down -v

stop:
	docker-compose stop

start:
	docker-compose start

logs:
	docker-compose logs -f $(CONTAINER_NAME)

### Symfony
cache-clear:
	$(PHP_CMD) bin/console cache:clear

debug-router:
	$(PHP_CMD) bin/console debug:router

### Migrations
migration-status:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations status

migration-generate:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations generate $(filter-out $@,$(MAKECMDGLOBALS))

migration-migrate:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations migrate $(filter-out $@,$(MAKECMDGLOBALS))

migration-reset:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations migrate first --no-interaction

### Testing
test-unit:
	$(PHP_CMD) vendor/bin/phpunit $(filter-out $@,$(MAKECMDGLOBALS))

### Vending machine specific commands
vending-machine-commands:
	$(PHP_CMD) bin/console list vending-machine
