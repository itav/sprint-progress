FROM php:7.1-apache

RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug

RUN sed -i '1 a xdebug.remote_enable=on' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN sed -i '1 a xdebug.remote_autostart=off' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update \
        && apt-get install -y \
            libmcrypt-dev \
            zlib1g-dev \
        && docker-php-ext-install -j$(nproc) mcrypt pdo pdo_mysql bcmath zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.5.2

RUN curl -s -f -L -o /tmp/installer.php https://raw.githubusercontent.com/composer/getcomposer.org/da290238de6d63faace0343efbdd5aa9354332c5/web/installer \
 && php -r " \
    \$signature = '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/.htaccess


ADD ./ /app/

ENV APACHE_DOCUMENT_ROOT /app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

WORKDIR /app