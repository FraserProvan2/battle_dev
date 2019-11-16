create_local:
	php -r "copy('.env.example', '.env');"
	composer install
	php artisan telescope:install
	php artisan key:generate
	npm install
	npm run dev

reset_db:
	php artisan migrate:fresh --seed

clear_cache: 
	composer dump-autoload
	php artisan cache:clear
	php artisan route:cache
	php artisan config:clear
	php artisan view:clear

test:
	vendor/bin/phpunit 
