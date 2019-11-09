docker_create:
	git clone https://github.com/Laradock/laradock.git
	cp laradock-env .env
	mv .env laradock
	cd laradock && docker-compose build nginx mysql workspace php-fpm

docker_up:
	cd laradock && docker-compose up -d nginx php-fpm mysql
	cd laradock && docker-compose exec workspace bash

docker_down:
	cd laradock && docker-compose down
