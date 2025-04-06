setup:
	@make build
	@make up 
	@make composer-update
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop

down:
	docker-compose down
up:
	docker-compose up -d
composer-update:
	docker exec laravel-be-docker bash -c "composer update"
data:
	docker exec laravel-be-docker bash -c "php artisan migrate"
	# docker exec laravel-be-docker bash -c "php artisan db:seed"


