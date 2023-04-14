build:
	composer install
	docker-compose up -d
	echo yes | php bin/console doctrine:migrations:migrate
	echo yes | php bin/console doctrine:fixtures:load
	symfony serve:start -d
	go run analytics_service/main.go

run_analytics_service:
	go run analytics_service/main.go
