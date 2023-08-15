UID = $$(id -u)
PWD = $$(pwd)

up:
	./cmd.sh dc up -d

down:
	./cmd.sh dc down

install: up
	./cmd.sh dc exec php-cli chown -R $(UID):$(UID) /.composer
	./cmd.sh dc exec --user $(UID) php-cli composer install
	./cmd.sh dc down && ./cmd.sh dc up -d

upgrade: up
	./cmd.sh dc down
	./cmd.sh dc pull
	./cmd.sh dc build
	./cmd.sh dc up -d
	./cmd.sh dc exec -u $(UID) php-cli composer update

stan: phpstan phpcs security-check
	@echo "Running static analysis tools"

shell: up
	./cmd.sh dc exec -u $(UID) php-cli sh

phpunit: up
	@echo "Running unit tests"
	./cmd.sh dc exec -u $(UID) php-cli composer test

phpstan: up
	@echo "Running phpstan"
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpstan

phpcs: up
	@echo "Running code sniffer"
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpcs

phpcbf: up
	./cmd.sh dc exec -u $(UID) php-cli composer run-script phpcbf

security-check:
	@echo "Running symfony security check"
	docker run -it --rm -v $(PWD):/src dancharousek/local-php-security-checker:v1.0.0

configure: cmd.sh
	./cmd.sh configure

serve: up
	./cmd.sh dc exec -u $(UID) php-cli php -S 0.0.0.0:80 -t examples
