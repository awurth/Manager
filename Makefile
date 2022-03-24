# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor update sf cc db-sql db-update db-fixtures test

## —— 🎵 🐳 The Symfony-docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

update: ## Updates vendors
	@$(COMPOSER) update

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Database —————————————————————————————————————————————————————————————————
db-create: ## Creates the configured database
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:database:create --if-not-exists --env=$(env)

db-drop: ## Drops the configured database
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:database:drop --force --if-exists --env=$(env)

db-sql: ## Dump the SQL needed to update the database schema to match the current mapping metadata
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:schema:update --dump-sql --env=$(env)

db-update: ## Execute the SQL needed to update the database schema to match the current mapping metadata
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:schema:update --force --env=$(env)

db-fixtures: ## Load data fixtures
	@$(eval env ?= dev)
	@$(SYMFONY) hautelook:fixtures:load --env=$(env)

## —— Tests ————————————————————————————————————————————————————————————————————
test: ## Run tests
	@$(eval c ?=)
	@$(PHP) bin/phpunit $(c)
