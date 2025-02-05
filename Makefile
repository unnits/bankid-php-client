UID = $$(id -u)
PWD = $$(pwd)

@all:
	less Makefile

# ------------------------------------------------------------------------------
# Project-related recipes
# ------------------------------------------------------------------------------

# SYNOPSIS: make up
#
# DESCRIPTION:
#
# Spin up the project
up:
	./cmd.sh dc up -d

# SYNOPSYS: make down
#
# DESCRIPTION:
#
# Stops and removes the project resources
down:
	./cmd.sh dc down

# SYNOPSYS: make configure
#
# DESCRIPTION:
configure: cmd.sh
	./cmd.sh configure

# SYNOPSYS: make install
#
# DESCRIPTION:
# Command used for the initial project setup.
# Should be called after `make configure`.
install: up
	./cmd.sh dc exec php-cli chown -R $(UID):$(UID) /.composer
	./cmd.sh dc exec --user $(UID) php-cli composer install
	./cmd.sh dc down && ./cmd.sh dc up -d

# SYNOPSYS: make upgrade
#
# DESCRIPTION:
upgrade: up
	./cmd.sh dc down
	./cmd.sh dc pull
	./cmd.sh dc build
	./cmd.sh dc up -d
	./cmd.sh dc exec -u $(UID) php-cli composer update

# SYNOPSYS: make stan
#
# DESCRIPTION:
stan: phpstan phpcs security-check
	@echo "Running static analysis tools"

# ------------------------------------------------------------------------------
# Daily workflow recipes
# Make rules usefull throughout your daily workflow
# ------------------------------------------------------------------------------

# SYNOPSYS: make shell [s=<service_name> [u=<user_uuid>]]
#
# DESCRIPTION:
# Command to attach to containers shell.
# After required parameter `s` user can specify `u` parameter,
# that determines the user to run the command under.
#
# EXAMPLE:
# make shell s=php
# make shell s=node u=0 <-- run as a root
shell: up
	./cmd.sh dc exec -u $(UID) php-cli sh

# SYNOPSYS: make serve
#
# DESCRIPTION: Spin up PHP's builtin server to view examples.
serve: up
	./cmd.sh dc exec -u $(UID) php-cli php -S 0.0.0.0:80 -t examples

# ------------------------------------------------------------------------------
# Quality checks rules
# ------------------------------------------------------------------------------

# SYNOPSYS: make phpstan
#
# DESCRIPTION:
phpstan: up
	@echo "Running phpstan"
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpstan

# SYNOPSYS: make phpstan-baseline
#
# DESCRIPTION:
phpstan-baseline: up
	@echo "Running phpstan"
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpstan-baseline

# SYNOPSYS: make phpcs
#
# DESCRIPTION:
phpcs: up
	@echo "Running code sniffer"
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpcs

# SYNOPSYS: make phpcbf
#
# DESCRIPTION:
phpcbf: up
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpcbf

# SYNOPSYS: make security-check
#
# DESCRIPTION:
security-check:
	@echo "Running symfony security check"
	docker run -it --rm -v $(PWD):/src dancharousek/local-php-security-checker:v1.0.0

# ------------------------------------------------------------------------------
# Tests related rules
# ------------------------------------------------------------------------------

phpunit: up
	@echo "Running unit tests"
	./cmd.sh dc exec -u $(UID) php-cli composer phpunit

