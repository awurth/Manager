# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
NODE_CONT = $(DOCKER_COMP) exec node

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console
YARN     = $(NODE_CONT) yarn

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor update sf cc db-sql db-update db-fixtures test

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

install: build up vendor db-update ## Installs the project

install-dev: build up vendor-dev db-update db-fixtures ## Installs the project for dev environment

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

vendor-dev: ## Install vendors according to the current composer.lock file
vendor-dev: c=install
vendor-dev: composer

update: update-php update-node ## Updates dependencies

update-php: ## Updates PHP vendors
	@$(COMPOSER) update

update-node: ## Updates Node modules
	@$(YARN) upgrade

yarn-dev:
	@$(YARN) dev

yarn-watch:
	@$(YARN) watch

yarn-build:
	@$(YARN) build

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

dump: ## Start a dump server that collects and displays dumps in a single place
	@$(SYMFONY) server:dump

## —— Database —————————————————————————————————————————————————————————————————
db-create: ## Creates the configured database
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:database:create --if-not-exists --env=$(env)

db-drop: ## Drops the configured database
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:database:drop --force --if-exists --env=$(env)

db-sql: ## Dump the SQL needed to update the database schema to match the current mapping metadata
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:schema:update --dump-sql --complete --env=$(env)

db-update: ## Execute the SQL needed to update the database schema to match the current mapping metadata
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:schema:update --force --complete --env=$(env)

db-fixtures: ## Load data fixtures
	@$(eval env ?= dev)
	@$(SYMFONY) doctrine:fixtures:load --env=$(env)

## —— Tests ————————————————————————————————————————————————————————————————————
test: phpunit behat ## Run tests

behat: ## Run Behat tests
	@$(eval c ?=)
	@$(PHP) vendor/bin/behat $(c)

phpunit: ## Run PHPUnit tests
	@$(eval c ?=)
	@$(PHP) bin/phpunit $(c)

## —— Quality tools ————————————————————————————————————————————————————————————
lint-php: ## Runs PHP CS Fixer
	@$(PHP) vendor/bin/php-cs-fixer fix

lint-twig: ## Runs Twig CS Fixer
	@$(PHP) vendor/bin/twig-cs-fixer lint --fix templates

lint: lint-php lint-twig  ## Runs CS Fixers

phpstan:
	@$(PHP) vendor/bin/phpstan analyse
