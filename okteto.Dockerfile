FROM sonarsoftware/customerportal:latest

RUN apt-get install -yqq --no-install-recommends \
      sudo \
  && rm -rf /var/lib/apt/lists/* \
  && echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

COPY deploy/dev/sonar-customerportal-dev.template /etc/nginx/conf.d/customerportal-dev.template
COPY deploy/dev/*.sh /etc/my_init.d/
