include .env
SHELL=/bin/bash
GREEN := $(shell tput -Txterm setaf 2)
BLUE := $(shell tput -Txterm setaf 6)
RESET := $(shell tput -Txterm sgr0)
TARGET_COLOR := $(BLUE)
.DEFAULT_GOAL := help

cache-clear: ## clear symfony cache
	bash -c 'bin/console cache:clear'

clear-thumbs: ## clear liip imagine cache
	bash -c 'bin/console liip:imagine:cache:remove'

entity: ## create doctrine enitity
	bash -c 'bin/console make:entity'

help: ## show this help
	@echo ""
	@echo "${GREEN}Symfony Base - Help${RESET}"
	@echo ""
	@grep -E '^[a-zA-Z_0-9%-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "${TARGET_COLOR}%-15s${RESET} %s\n", $$1, $$2}'

install: ## install dependencies
	bash -c 'yarn'
	bash -c 'composer install'

migrate: ## migrate database
	bash -c 'bin/console doctrine:migrations:migrate'

migrate-first: ## revert database
	bash -c 'bin/console doctrine:migrations:migrate first'

migration: ## create migration
	bash -c 'bin/console doctrine:migrations:diff'

phpstan: ## execute php analysis
	bash -c 'vendor/bin/phpstan analyse src --level=${PHPSTAN_LEVEL}'

startup: ## initialize project
	bash -c 'yarn startup'

webpack: ## build assets dev
	bash -c 'yarn encore dev'

webpack-p: ## build assets prod
	bash -c 'yarn encore prod'

webpack-watch: ## watch assets
	bash -c 'yarn encore dev --watch'
