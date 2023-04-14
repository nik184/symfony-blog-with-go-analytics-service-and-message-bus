build:
	composer install
	docker-compose up -d
	php bin/console doctrine:migrations:migrate
	php bin/console doctrine:fixtures:load
	symfony serve:start -d
	go run analytics_service/main.go
