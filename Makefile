start:
	php artisan serve

setup:
	composer install
	cp -n .env.example .env || true
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate
	php artisan db:seed
	npm install
	npm run dev

migrate:
	php artisan migrate

migrate-reset:
	php artisan migrate:reset

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	php artisan test

test-coverage:
	composer phpunit tests -- --coverage-clover build/logs/clover.xml

lint:
	composer phpcs

lint-fix:
	composer phpcbf
