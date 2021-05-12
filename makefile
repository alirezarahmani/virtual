# PROJECT CONFIGURATION
export VERSION = 0.0.1
export PROJECT_NAME = worker


# shared config (do not set any project config here)
export PROJECT_IP = 0.0.0.0
export SHELL = bash
export LOCAL_DEV_DIR = $(shell pwd)

#DOCKER
DOCKER = docker
DOCKER_COMPOSE = docker-compose
CLI = $(DOCKER_COMPOSE) exec -T ${PROJECT_NAME}

CONSOLE = $(CLI) php bin/console

#COLORS
GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RED    := $(shell tput -Txterm setaf 5)
RESET  := $(shell tput -Txterm sgr0)

# Add the following 'help' target to your Makefile
# And add help text after each target name starting with '\#\#'
# A category can be added with @category
HELP_FUN = \
        %help; \
        while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
        print "usage: make [target]\n\n"; \
        for (sort keys %help) { \
        print "${WHITE}$$_:${RESET}\n"; \
        for (@{$$help{$$_}}) { \
        $$sep = " " x (32 - length $$_->[0]); \
        print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
        }; \
        print "\n"; }

help: ##@other Show this help
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
.PHONY: help

start: ##@development start project
	$(DOCKER_COMPOSE) up -d
.PHONY: start

stop: ##@development stop project
	$(DOCKER_COMPOSE) stop
.PHONY: stop

down: ##@development delete project container
	$(DOCKER_COMPOSE) down
.PHONY: down

build: ##@development build container
	$(DOCKER_COMPOSE) build
.PHONY: ps

ps: ##@development show running container
	$(DOCKER_COMPOSE) ps
.PHONY: ps

logs: ##@development show server logs
	$(DOCKER_COMPOSE) logs -f
.PHONY: logs

cli: ##@development get shell
	$(DOCKER_COMPOSE) exec ${PROJECT_NAME} $(SHELL)
.PHONY: cli

composer-install: ##@development run composer install
	$(CLI) composer install
.PHONY: composer-install

composer-update: ##@development run composer update
	$(CLI) composer update
.PHONY: composer-update

composer-dump-autoload: ##@development run composer dump-autoload
	$(CLI) composer dump-autoload -a
.PHONY: composer-dump-autoload

