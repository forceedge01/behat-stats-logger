.PHONY: test
test:
	./vendor/bin/behat -c behat.yml

install: composer.lock
	composer install

update:
	composer update
