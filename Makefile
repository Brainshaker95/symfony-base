include .env

cache-clear:
	bash -c 'bin/console cache:clear'

clear-thumbs:
	bash -c 'bin/console liip:imagine:cache:remove'

entity:
	bash -c 'bin/console make:entity'

install:
	bash -c 'yarn'
	bash -c 'composer install'

migrate:
	bash -c 'bin/console doctrine:migrations:migrate'

migrate-first:
	bash -c 'bin/console doctrine:migrations:migrate first'

migration:
	bash -c 'bin/console doctrine:migrations:diff'

phpstan:
	bash -c 'vendor/bin/phpstan analyse src --level=${PHPSTAN_LEVEL}'

startup:
	bash -c 'yarn startup'

webpack:
	bash -c 'yarn encore dev'

webpack-p:
	bash -c 'yarn encore prod'

webpack-watch:
	bash -c 'yarn encore dev --watch'
