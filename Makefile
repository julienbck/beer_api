.PHONY: docker-build start stop down install setup php destroy down migrate diff require help bash require reset_bdd fixtures

.DEFAULT_GOAL: help

CONTAINER="php"

COMMAND=""

COMPOSE_DEV_FILE= 'docker-compose-dev.yml'

RUN_ACL:= sudo setfacl -dR -m u:$(whoami):rwX -m u:1000:rwX ./ && sudo setfacl -R -m u:$(whoami):rwX -m u:1000:rwX ./

PROJECT_PATH_BACK = $(shell echo ${PWD})

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

build: ## Lance le build des conteneur docker
	docker-compose -f docker-compose.yml build

start: ## Demmare les conteneurs docker
	docker-compose -f docker-compose.yml up -d

stop: ## Arrete les conteneurs docker
	docker-compose -f docker-compose.yml stop

destroy: ## Arretes les conteneurs en detruisant les conteneurs, volumes et network associee.
	docker-compose -f docker-compose.yml  down --volumes

composer.lock: composer.json ## Composer update
	docker run --rm -v ${PWD}:/app composer:latest composer update --with-dependencies --no-interaction --ignore-platform-reqs

vendor: composer.json ## Composer install
	docker run --rm -v ${PWD}:/app composer:latest composer install --no-interaction --ignore-platform-reqs

require: ## Composer require
	docker run --rm -v ${PWD}:/app composer:latest composer require ${PKG} --no-interaction --ignore-platform-reqs

dump-autoload: ## Composer dump-autoload
	docker run --rm -v ${PWD}:/app composer:latest composer dump-autoload -a

bash: ## Connexion a un conteneur par defaut php. Renseigner CONTAINER="nom du conteneur" pour ce connecter a un conteneur
	docker-compose -f docker-compose.yml  exec -u 33:33 $(CONTAINER) bash

setup: acl .env build start ## Tache d'initialisation de l'environnement

.env.local: .env
	cp .env .env.local

acl: ## Donne les droits d'ecriture sur le dossier
	$(RUN_ACL)