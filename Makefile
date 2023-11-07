include .env

install:
	@$(MAKE) -s down
	@$(MAKE) -s docker-build
	@$(MAKE) -s composer-install
	@$(MAKE) -s up

docker-build: \
	docker-build-php-cli

up:
	@docker-compose up -d

down:
	@docker-compose down --remove-orphans

restart: down up

docker-build-php-cli:
	@docker build --target=cli -t ${REGISTRY}/${PHP_CLI_CONTAINER_NAME}:${IMAGE_TAG} -f ./docker/Dockerfile .

docker-logs:
	@docker-compose logs -f

app-php-cli-exec:
	@docker-compose run --rm php-cli $(cmd)

composer-install:
	$(MAKE) app-php-cli-exec cmd="composer install"
