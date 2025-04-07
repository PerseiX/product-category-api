GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RED    := $(shell tput -Txterm setaf 1)
RESET  := $(shell tput -Txterm sgr0)


.PHONY: install
install:
	@echo "${GREEN}Application is running...${RESET}"
	docker compose up -d

	@echo "${GREEN}Packages are installing...${RESET}"
	docker compose  run --rm --no-deps --remove-orphans --name app-install-composer product_api composer install

	@echo "${GREEN}Dev and test database are creating...${RESET}"
	docker compose  run --rm --no-deps --remove-orphans --name database-create product_api bin/console doctrine:database:create --if-not-exists
	docker compose  run --rm --no-deps --remove-orphans --name database-create product_api bin/console doctrine:database:create --if-not-exists --env=test

	@echo "${GREEN}Migrations are running...${RESET}"
	docker compose  run --rm --no-deps --remove-orphans --name database-create product_api bin/console doctrine:schema:update --force --env=test
	docker compose  run --rm --no-deps --remove-orphans --name database-create product_api bin/console doctrine:migrations:migrate --no-interaction


.PHONY: stop
stop:
	@echo "${GREEN}Cleaning after tests...${RESET}"
	docker compose down --remove-orphans