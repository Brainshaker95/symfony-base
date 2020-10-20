include .env
ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
$(eval $(RUN_ARGS):;@:)
SHELL=/bin/bash
GREEN := $(shell tput -Txterm setaf 2)
BLUE := $(shell tput -Txterm setaf 6)
RESET := $(shell tput -Txterm sgr0)
TARGET_COLOR := $(BLUE)
.DEFAULT_GOAL := help

help: ## show this help
	@echo ""
	@echo "${GREEN}Symfony Base - Help${RESET}"
	@echo ""
	@grep -hE '^[a-zA-Z_0-9%-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${TARGET_COLOR}%-15s${RESET} %s\n", $$1, $$2}'

cache-clear: ## clear symfony cache
	@make -s console cache:clear

clear-thumbs: ## clear liip imagine cache
	@make -s console liip:imagine:cache:remove

entity: ## create doctrine enitity
	@make -s console make:entity

migrate: ## migrate database
	@make -s console doctrine:migrations:migrate

migrate-first: ## revert database
	@make -s console 'doctrine:migrations:migrate first'

migrate-next: ## migrate database to next version
	@make -s console 'doctrine:migrations:migrate next'

migrate-prev: ## migrate database to previous version
	@make -s console 'doctrine:migrations:migrate prev'

migration: ## create migration
	@make -s console 'doctrine:migrations:diff'

phpstan: ## execute php analysis
	bash -c 'vendor/bin/phpstan analyse src --level=${PHPSTAN_LEVEL}'

startup: ## initialize project
	bash -c 'yarn startup'


## Docker
#################################

generate-ssl-key: ## generate ssl for localhost
	@bash -c "cd ./.docker/web/ssl-keys && mkcert -cert-file localhost.crt -key-file localhost.key localhost 127.0.0.1 ::1 ${DOCKER_DOMAIN}"

init: build boot vendor webpack ## initializes docker machine
	sleep 10 # wait that db is ready
	@make -s migrate

build: generate-ssl-key ## build all docker containers
	bash -c "docker-compose build"

clear-volumes: ## deletes all volumes of this project
	docker-compose down -v

reset: clear-volumes init ## clear volumes and run init command

vendor: ## install dependencies
	@make -s yarn
	@make -s vendor-composer

vendor-composer: ## installs composer dependencies
	@make -s composer install

vendor-yarn: ## install node modules
	@make -s yarn

## Docker booting
#################################

boot:
	bash -c "docker-compose up -d"

up: boot vendor-composer ## start docker

down: ## stop  all docker containers
	bash -c "docker-compose down"

restart: down up ## restart all docker containers

## Docker login to containers
#################################

bash: ## open docker bash via ssh
	bash -c "docker-compose exec -u app web bash"

bash-root: ## open docker bash via ssh as root
	bash -c "docker-compose exec -u root web bash"

bash-db: ## open docker bash via ssh as root
	bash -c "docker-compose exec db bash"

## Forwarding commands
#################################
php: ## forward php command to container
	docker-compose exec -u app web bash -c 'php ${ARGS}'

composer: ## forward composer command to container
	docker-compose exec -u app web bash -c 'composer ${ARGS}'

yarn: ## forward yarn command to container
	docker-compose exec -u app web bash -c '. /usr/local/nvm/nvm.sh && yarn ${ARGS}'

console: ## forward console command to container
	docker-compose exec -u app web bash -c 'php bin/console ${ARGS}'

## Builds Assets / Node commands
#################################

webpack: ## build assets
	@make -s yarn "dev"
webpack-watch: ## build assets and start watching for file changes
	@make -s yarn "watch-poll"
webpack-production: ## build assets and start watching for file changes
	@make -s yarn "production"

## PHPUNIT
#################################

test: ## run phpunit tests
	@make -s php './vendor/bin/phpunit -v --colors=never --stderr'

# match all unknown tasks
%:
	@:
