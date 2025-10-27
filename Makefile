up:
	docker compose -f .docker/docker-compose.yml up -d --build
cs:
	vendor/bin/phpcs --standard=.phpcs.xml.dist
cbf:
	vendor/bin/phpcbf
psalm:
	- vendor/bin/psalm
unit:
	vendor/bin/phpunit --testdox --colors=always
