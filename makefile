VERSION	:= 0.1.0
PHP_MYSQL_NAME := civisblockchain/php-mysql
PHP_MYSQL_IMG := ${PHP_MYSQL_NAME}:${VERSION}
PHP_MYSQL_IMG_LATEST := ${PHP_MYSQL_NAME}:latest


build: build-docker-php-mysql

push: push-docker-php-mysql

tag-latest: tag-latest-php-mysql

build-docker-php-mysql:
	@docker build -f Dockerfile -t ${PHP_MYSQL_IMG} .

tag-latest-php-mysql:
	@docker tag ${PHP_MYSQL_IMG} ${PHP_MYSQL_IMG_LATEST}
	@docker push ${PHP_MYSQL_IMG_LATEST}

push-docker-php-mysql:
	@docker push ${PHP_MYSQL_IMG}