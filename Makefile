cache-clear:
	php bin/console cache:clear

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
	php bin/console make:migration

phpstan:
	bash -c 'vendor/bin/phpstan analyse src --level=8'

webpack:
	yarn encore dev

webpack-p:
	yarn encore production

webpack-watch:
	yarn encore dev --watch
