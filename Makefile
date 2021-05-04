start:
	php artisan serve --host 127.0.0.1:8000

setup:
	composer install
	cp -n .env.example .env|| true
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate
	php artisan db:seed
	npm install

migrate:
	php artisan migrate

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	php artisan test

test-coverage:
    composer phpunit tests -- --coverage-clover build/logs/clover.xml

lint:
	composer run-script phpcs -- --standard=PSR12 routes tests

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 routes tests
