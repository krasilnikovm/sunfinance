cs-check:
	./vendor/bin/php-cs-fixer check

cs-fix:
	./vendor/bin/php-cs-fixer fix

phpstan:
	./vendor/bin/phpstan

unit-test:
	./vendor/bin/phpunit --testsuite=unit-test

fixtures:
	php bin/console doctrine:fixtures:load --purge-with-truncate --no-interaction

composer:
	composer install

application-test:
	php bin/console doctrine:database:drop --no-interaction --if-exists --force --env=test
	php bin/console doctrine:database:create --no-interaction --if-not-exists --env=test
	php bin/console doctrine:migrations:migrate --no-interaction --env=test
	./vendor/bin/phpunit --testsuite=application-test
