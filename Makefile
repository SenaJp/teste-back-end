SHELL := /bin/bash

# Detect docker compose CLI (docker compose vs docker-compose)
ifneq (,$(shell command -v docker-compose 2>/dev/null))
  DC := docker-compose
else
  DC := docker compose
endif

APP_SERVICE := app
WEB_SERVICE := web
DB_SERVICE  := db

.PHONY: help up down destroy build restart logs app-logs web-logs install key migrate seed fresh test import import-one art sh perms setup config-clear cache-clear

help: ## Show this help
	@awk 'BEGIN {FS = ":.*##"}; /^[a-zA-Z0-9_\-]+:.*##/ {printf "\033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start containers in background (build if needed)
	$(DC) up -d --build

down: ## Stop containers
	$(DC) down

destroy: ## Stop containers and remove volumes (DB data)
	$(DC) down -v

restart: ## Restart all services
	$(DC) restart

build: ## Rebuild only the app image (no cache)
	$(DC) build --no-cache $(APP_SERVICE)

logs: ## Tail all services logs
	$(DC) logs -f

app-logs: ## Tail app service logs
	$(DC) logs -f $(APP_SERVICE)

web-logs: ## Tail web service logs
	$(DC) logs -f $(WEB_SERVICE)

install: ## Install PHP dependencies with Composer
	$(DC) exec $(APP_SERVICE) composer install

key: ## Generate APP_KEY
	$(DC) exec $(APP_SERVICE) php artisan key:generate

migrate: ## Run database migrations
	$(DC) exec $(APP_SERVICE) php artisan migrate

seed: ## Seed the database
	$(DC) exec $(APP_SERVICE) php artisan db:seed

fresh: ## Fresh migrate and seed
	$(DC) exec $(APP_SERVICE) php artisan migrate:fresh --seed

test: ## Run test suite
	$(DC) exec $(APP_SERVICE) php artisan test

import: ## Import all products from external API
	$(DC) exec $(APP_SERVICE) php artisan products:import

import-one: ## Import a single product by external id (usage: make import-one ID=1)
	@if [ -z "$(ID)" ]; then echo "Usage: make import-one ID=<external_id>"; exit 1; fi
	$(DC) exec $(APP_SERVICE) php artisan products:import --id=$(ID)

art: ## Run an artisan command (usage: make art CMD="route:list")
	@if [ -z "$(CMD)" ]; then echo "Usage: make art CMD=\"route:list\""; exit 1; fi
	$(DC) exec $(APP_SERVICE) php artisan $(CMD)

config-clear: ## Clear Laravel config cache (use after changing env)
	$(DC) exec $(APP_SERVICE) php artisan config:clear

cache-clear: ## Clear Laravel application cache
	$(DC) exec $(APP_SERVICE) php artisan cache:clear

sh: ## Open a shell in the app container
	$(DC) exec $(APP_SERVICE) sh

perms: ## Fix storage and cache permissions
	$(DC) exec $(APP_SERVICE) sh -lc 'chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwx storage bootstrap/cache'

setup: ## One-shot: up, install deps, key generate, migrate, perms, clear caches, restart
	@if [ ! -f .env ]; then cp .env.example .env && echo "Created .env from .env.example"; fi
	$(MAKE) up
	$(MAKE) install
	$(MAKE) key
	$(MAKE) migrate
	@echo "App is up at http://localhost:8080"
