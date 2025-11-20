.PHONY: help build up down restart logs shell composer npm artisan migrate seed test clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker images
	docker-compose build --no-cache

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

restart: ## Restart Docker containers
	docker-compose restart

logs: ## Show Docker logs
	docker-compose logs -f

shell: ## Access app container shell (Docker only)
	docker-compose exec app bash

composer: ## Run composer install (local)
	composer install

npm-install: ## Run npm install (local)
	npm install

npm-dev: ## Run npm dev (watch mode) - local
	npm run dev

npm-build: ## Run npm build (local)
	npm run build

artisan: ## Run artisan command (usage: make artisan cmd="migrate") - local
	php artisan $(cmd)

migrate: ## Run database migrations (local)
	php artisan migrate

migrate-fresh: ## Fresh migration (drops all tables) - local
	php artisan migrate:fresh

seed: ## Seed database (local)
	php artisan db:seed

serve: ## Start Laravel development server (local)
	php artisan serve

setup: ## Initial setup (install dependencies, generate key, migrate)
	docker-compose up -d
	sleep 5
	composer install
	npm install
	php artisan key:generate
	php artisan migrate
	@echo "Setup complete! Run 'php artisan serve' to start the app"

setup-docker: ## Setup with Docker (full Docker setup)
	docker-compose -f docker-compose.yml -f docker-compose.full.yml up -d
	sleep 5
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	@echo "Setup complete! Access the app at http://localhost:8000"

test: ## Run tests (local)
	php artisan test

clear: ## Clear all caches (local)
	php artisan config:clear
	php artisan cache:clear
	php artisan route:clear
	php artisan view:clear
	php artisan optimize:clear

clean: ## Stop containers and remove volumes
	docker-compose down -v

ps: ## Show running containers
	docker-compose ps

