start:
	php artisan serve --host 127.0.0.1:8000

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	php artisan test

lint:
	composer run-script phpcs -- --standard=PSR12 routes

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 routes
