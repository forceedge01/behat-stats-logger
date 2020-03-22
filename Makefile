.PHONY: test
test:
	./vendor/bin/behat -c behat.yml
	./vendor/bin/behat -c behat.yml --tags multiFiles
	./vendor/bin/behat -c behat.yml --tags singleFile
	./vendor/bin/behat -c behat.yml --tags empty

.PHONY: test-single
test-single:
	./vendor/bin/behat -c behat.yml

install: composer.lock
	composer install

update:
	composer update
