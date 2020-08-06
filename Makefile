include .env

cache-clear:
	php bin/console cache:clear

clear-thumbs:
	php bin/console liip:imagine:cache:remove

entity:
	php bin/console make:entity

install:
	yarn
	composer install

migrate:
	php bin/console doctrine:migrations:migrate

migrate-first:
	php bin/console doctrine:migrations:migrate first

migration:
	php bin/console doctrine:migrations:diff

phpstan:
	bash -c 'vendor/bin/phpstan analyse src --level=${PHPSTAN_LEVEL}'

startup:
	yarn startup

webpack:
	yarn encore dev

webpack-p:
	yarn encore prod

webpack-watch:
	yarn encore dev --watch
