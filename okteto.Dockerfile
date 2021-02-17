FROM sonarsoftware/customerportal:latest

COPY --chown=www-data --from=composer:1.8.4 /usr/bin/composer /usr/local/bin/composer

RUN install_clean \
      php7.3-dom \
      sudo \
 && echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

COPY deploy/dev/sonar-customerportal-dev.template /etc/nginx/conf.d/customerportal-dev.template
COPY deploy/dev/*.sh /etc/my_init.d/

RUN COMPOSER_CACHE_DIR=/dev/null setuser www-data composer install --no-interaction --no-scripts
