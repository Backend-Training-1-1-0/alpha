# PHP CLI
FROM php:8.2-cli-alpine3.17 as cli

COPY ./docker/php/cli/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer-2 opcache pdo_mysql \
    redis \
    pcntl

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1

RUN apk --no-cache add bash shadow

COPY ./ /app

WORKDIR /app

# Находим имя пользователя с UID 33
RUN user_to_delete=$(getent passwd 33 | cut -d: -f1) && \
    if [ "$user_to_delete" ]; then \
        userdel -r "$user_to_delete"; \
    fi

# Теперь вы можете изменить UID и GID для www-data
RUN usermod -u 33 www-data && groupmod -g 33 www-data

RUN rm -rf /app/vendor \
    && composer --no-ansi --no-interaction install --no-progress
