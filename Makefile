.PHONY: test
test:
	./vendor/bin/behat -c behat.yml
	./vendor/bin/behat -c behat.yml --tags multiFiles
	./vendor/bin/behat -c behat.yml --tags singleFile
	./vendor/bin/behat -c behat.yml --tags empty

install: composer.lock
	composer install

update:
	composer update
