create_local:
	#php -r \"copy('.env.example', '.env');\"
	composer install
	php artisan telescope:install
	php artisan key:generate
	npm install
	npm run dev

test:
	vendor/bin/phpunit 

clear_cache: 
	php artisan cache:clear
	php artisan route:cache
	php artisan config:clear
	php artisan view:clear 
	composer dump-autoload