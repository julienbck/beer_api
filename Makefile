.DEFAULT_GOAL := help
.PHONY: all build start stop reset install cc db

COMPOSE=docker-compose
RUN=$(COMPOSE) run --rm php
EXEC=docker exec -ti php

serve: reset build start install db ## Prepare the dev env

build: ## Build docker images
	$(COMPOSE) build

start: ## Start the app
	$(COMPOSE) up -d --remove-orphans

stop: ## Stop the app
	$(COMPOSE) stop

down: ## Stop the app and remove containers
	$(COMPOSE) down

reset: ## Remove all containers
	$(COMPOSE) kill
	$(COMPOSE) rm -fv

install: ## Install PHP and frontend dependencies, also install and dump assets
	$(RUN) composer install --no-interaction

cc: ## Run cache clear and cache warmup
	$(RUN) bin/console cache:clear --no-warmup
	$(RUN) bin/console cache:warmup

db: ## Initialize the database
	$(RUN) bin/console doctrine:database:drop --force --if-exists -n
	$(RUN) bin/console doctrine:database:create -n
	$(RUN) bin/console doctrine:schema:create -n
	$(RUN) bin/console load:data-fixture open-beer-database.csv

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

