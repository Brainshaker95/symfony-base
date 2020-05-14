cache-clear:
	php bin/console cache:clear

install:
	yarn
	composer install

migrate:
	php bin/console doctrine:migrations:migrate

migrate-first:
	php bin/console doctrine:migrations:migrate first

phpstan:
	bash -c 'vendor/bin/phpstan analyse src --level=8'

webpack:
	yarn encore dev

webpack-p:
	yarn encore production

webpack-watch:
	yarn encore dev --watch
