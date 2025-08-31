FROM laradock/php-fpm:latest-8.2
USER root

RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install pcntl
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/project
COPY . /var/project

RUN chmod +x /var/project/.docker/install.sh \
 && chmod -R 775 /var/project/storage /var/project/bootstrap/cache \
    && chown -R www-data:www-data /var/project/storage /var/project/bootstrap/cache
RUN bash /var/project/.docker/install.sh
