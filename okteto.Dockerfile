FROM sonarsoftware/customerportal:latest

COPY deploy/dev/sonar-customerportal-dev.template /etc/nginx/conf.d/customerportal-dev.template
COPY deploy/dev/*.sh /etc/my_init.d/
