FROM sonarsoftware/customerportal:latest

COPY --chown=www-data --from=composer:2.2.12 /usr/bin/composer /usr/local/bin/composer

RUN install_clean \
      php7.3-dom \
      sudo \
 && echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

COPY deploy/dev/sonar-customerportal-dev.template /etc/nginx/conf.d/customerportal-dev.template
COPY deploy/dev/*.sh /etc/my_init.d/
COPY deploy/dev/99-disable-opcache.ini /etc/php/7.3/fpm/conf.d/

RUN COMPOSER_CACHE_DIR=/dev/null setuser www-data composer install --no-interaction --no-scripts
