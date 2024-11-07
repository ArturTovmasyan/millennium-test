APP_CONTAINER_NAME=symfony_app

# Команда для сборки и запуска контейнеров
up:
	@echo "Building the project via Docker..."
	@docker compose up -d --build && sleep 2
	docker exec -it $(APP_CONTAINER_NAME) composer install && sleep 3 && $(MAKE) run-migrations

local:
	@echo "Building the project in local host environment..."
	composer install && sleep 3 && $(MAKE) run-migrations

# Команда для запуска проекта
start:
	@echo "Starting the project..."
	docker compose up -d;

stop:
	@echo "Stopping the project..."
	docker compose stop;

# Команда для остановки и удаления контейнеров
down:
	@echo "Stopping the project..."
	docker compose down -v

# Команда для входа в контейнер проектов
exec:
	@echo "Entering the user container..."; \
	docker exec -it $(APP_CONTAINER_NAME) bash; \

build:
	@echo "Building the containers..."
	docker compose build

run-migrations:
	@echo "Run $(APP_CONTAINER_NAME) container migrations..."
	docker exec -it $(APP_CONTAINER_NAME) php bin/console doctrine:database:create --if-not-exists
	@if docker exec -it $(APP_CONTAINER_NAME) php bin/console doctrine:migrations:status | grep -q "New"; then \
		echo "New migrations found. Running migrations..."; \
		docker exec -it $(APP_CONTAINER_NAME) php bin/console doctrine:migrations:migrate --no-interaction; \
	else \
		echo "No new migrations to run."; \
	fi

.PHONY: up start stop exec down build run-migrations
