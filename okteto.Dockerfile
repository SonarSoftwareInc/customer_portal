FROM okteto.dev/sonar-customer-portal-base:okteto

COPY --chown=www-data --from=composer:2.5.8 /usr/bin/composer /usr/local/bin/composer

RUN install_clean \
      sudo \
 && echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

COPY deploy/dev/sonar-customerportal-dev.template /etc/nginx/conf.d/customerportal-dev.template
COPY deploy/dev/*.sh /etc/my_init.d/
COPY deploy/dev/99-disable-opcache.ini /etc/php/8.2/fpm/conf.d/
COPY deploy/dev/www-env-vars-dev.conf /etc/php/8.2/fpm/pool.d/www-env-vars.conf

RUN COMPOSER_CACHE_DIR=/dev/null setuser www-data composer install --no-interaction --no-scripts
