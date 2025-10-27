init: up db
up:
	docker compose -f .docker/docker-compose.yml up -d --build
db:
	docker exec test_php composer db:reset
phpunit:
	docker exec test_php php vendor/bin/phpunit
