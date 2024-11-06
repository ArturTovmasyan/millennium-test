USER_CONTAINER_NAME=user-symfony
EMAIL_CONTAINER_NAME=email-symfony
GATEWAY_CONTAINER_NAME=gateway-symfony
FRONT_CONTAINER_NAME=front-symfony
DATE := $(shell date +%Y-%m-%d)

# Команда для сборки и запуска контейнеров
up:
	@echo "Building the project..."
	@cd user && cp .env.test .env
	@docker network inspect shared-network >/dev/null 2>&1 || docker network create shared-network
	@cd user && docker compose up -d --build && sleep 5
	@docker exec -it $(USER_CONTAINER_NAME) composer install
	@docker exec -it $(USER_CONTAINER_NAME) php bin/console doctrine:schema:update --force
	@docker exec -it $(USER_CONTAINER_NAME) php bin/console lexik:jwt:generate-keypair --skip-if-exists

	# Команда для запуска email контейнер
	@cd email && cp .env.test .env
	@cd email && docker compose up -d --build && sleep 5
	@docker exec -it $(EMAIL_CONTAINER_NAME) composer install && $(MAKE) run-email-migrations

    # Команда для запуска gateway контейнер
	@cd gateway && cp .env.test .env
	@cd gateway && docker compose up -d --build && sleep 5
	@docker exec -it $(GATEWAY_CONTAINER_NAME) composer install
	$(MAKE) start-workers

	# Команда для запуска front контейнер
	@cd front && cp .env.test .env
	@cd front && docker compose up -d --build && sleep 5
	@docker exec -it $(FRONT_CONTAINER_NAME) composer install
	@docker exec -it $(FRONT_CONTAINER_NAME) npm install && docker exec -it $(FRONT_CONTAINER_NAME) npm run dev

# Команда для запуска проекта
start:
	@echo "Starting the project..."
	@cd user && docker compose up -d;
	@cd email && docker compose up -d;
	@cd gateway && docker compose up -d;
	@cd front && docker compose up -d;

stop:
	@echo "Stopping the project..."
	@cd user && docker compose stop;
	@cd email && docker compose stop;
	@cd gateway && docker compose stop;
	@cd front && docker compose stop;

# Команда для остановки и удаления контейнеров
down:
	$(MAKE) stop-workers
	@echo "Stopping the project..."
	@cd user && docker compose down -v
	@cd email && docker compose down -v
	@cd gateway && docker compose down -v
	@cd front && docker compose down -v

# Команда для входа в контейнер проектов
exec:
	@if [ "$(filter user,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the user container..."; \
		docker exec -it $(USER_CONTAINER_NAME) bash; \
	elif [ "$(filter email,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the email container..."; \
		docker exec -it $(EMAIL_CONTAINER_NAME) bash; \
	elif [ "$(filter gateway,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the gateway container..."; \
		docker exec -it $(GATEWAY_CONTAINER_NAME) bash; \
	elif [ "$(filter front,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the front container..."; \
		docker exec -it $(FRONT_CONTAINER_NAME) bash; \
	else \
		echo "Unknown param: $(filter-out exec,$(MAKECMDGOALS)). Please use one of the existing container names"; \
		exit 1; \
	fi

# Основная цель для логов
log:
	@if [ "$(filter user,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the user log..."; \
		cd user && tail -f "var/log/error/dev-$(DATE).log"; \
	elif [ "$(filter email,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the email log..."; \
		cd email && tail -f "var/log/error/dev-$(DATE).log"; \
	elif [ "$(filter gateway,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the gateway log..."; \
		cd gateway && tail -f "var/log/error/dev-$(DATE).log"; \
	elif [ "$(filter front,$(MAKECMDGOALS))" != "" ]; then \
		echo "Entering the front log..."; \
		cd front && tail -f "var/log/error/dev-$(DATE).log"; \
	else \
		echo "Unknown param: $(filter-out log,$(MAKECMDGOALS)). Please use one of the existing container names"; \
		exit 1; \
	fi

build:
	@echo "Building the containers..."
	@cd user && docker compose build
	@cd email && docker compose build
	@cd gateway && docker compose build
	@cd front && docker compose build

run-migrations:
	@echo "Run $(CONTAINER_NAME) container migrations..."
	docker exec -it $(CONTAINER_NAME) php bin/console doctrine:database:create --if-not-exists
	@# Проверяем наличие новых миграций
	@if docker exec -it $(CONTAINER_NAME) php bin/console doctrine:migrations:status | grep -q "New"; then \
		echo "New migrations found. Running migrations..."; \
		docker exec -it $(CONTAINER_NAME) php bin/console doctrine:migrations:migrate --no-interaction; \
		docker exec -it $(CONTAINER_NAME) php bin/console doctrine:schema:update --force; \
	else \
		echo "No new migrations to run."; \
	fi

# Запуск миграций для user
run-user-migrations:
	$(MAKE) run-migrations CONTAINER_NAME=$(USER_CONTAINER_NAME)

# Запуск миграций для email
run-email-migrations:
	$(MAKE) run-migrations CONTAINER_NAME=$(EMAIL_CONTAINER_NAME)

start-workers:
	@echo "Starting Symfony Messenger workers in the background..."
	@docker exec -d $(GATEWAY_CONTAINER_NAME) php bin/console messenger:consume response --time-limit=3600 --memory-limit=128M > /dev/null 2>&1 & \
	docker exec -d $(USER_CONTAINER_NAME) php bin/console messenger:consume user --time-limit=3600 --memory-limit=128M > /dev/null 2>&1 & \
	docker exec -d $(EMAIL_CONTAINER_NAME) php bin/console messenger:consume email --time-limit=3600 --memory-limit=128M > /dev/null 2>&1 & \
	wait
	@echo "All workers started..."

stop-workers:
	@echo "Stopping Symfony Messenger workers..."
	@docker exec -it $(GATEWAY_CONTAINER_NAME) php bin/console messenger:stop-worker
	@docker exec -it $(USER_CONTAINER_NAME) php bin/console messenger:stop-worker
	@docker exec -it $(EMAIL_CONTAINER_NAME) php bin/console messenger:stop-worker

.PHONY: up start stop exec down log build run-migrations run-user-migrations run-email-migrations generate-keypair
