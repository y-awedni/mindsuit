# Mindsuit - developer shortcuts.
# Requires Docker. On Windows without `make`, run the docker compose commands directly
# (see docs/DEVELOPMENT.md).

DC = docker compose
PHP = $(DC) exec -T php

.DEFAULT_GOAL := help

.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: ## First-time setup: create docker/.env from the example
	@test -f docker/.env || cp docker/.env.example docker/.env
	@echo "docker/.env ready. Now run: make up && make install"

.PHONY: up
up: ## Build and start the stack
	$(DC) up -d --build

.PHONY: down
down: ## Stop the stack
	$(DC) down

.PHONY: install
install: ## Install PHP dependencies
	$(PHP) composer install --no-interaction --prefer-dist
	$(PHP) php bin/console cache:clear

.PHONY: cache
cache: ## Clear and warm the Symfony cache (dev)
	$(PHP) php bin/console cache:clear --env=dev

.PHONY: console
console: ## Run a Symfony console command, e.g. make console c="doctrine:schema:validate"
	$(PHP) php bin/console $(c)

.PHONY: sh
sh: ## Open a shell in the PHP container
	$(DC) exec php bash

.PHONY: logs
logs: ## Tail container logs
	$(DC) logs -f

.PHONY: db-reset
db-reset: ## Drop the database volume and re-seed from docker/mysql/init dumps
	$(DC) down -v
	$(DC) up -d database

.PHONY: db-import
db-import: ## Import a dump: make db-import f=path/to/dump.sql
	$(DC) exec -T database sh -c 'exec mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE' < $(f)

.PHONY: test
test: ## Run the PHPUnit test suite
	$(PHP) vendor/bin/phpunit -c phpunit.xml.dist
