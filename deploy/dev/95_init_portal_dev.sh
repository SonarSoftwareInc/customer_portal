#!/bin/bash
set -euf -o pipefail

if [ "${APP_ENV-}" = "local" ]
then
    envsubst \$NGINX_HOST < /etc/nginx/conf.d/customerportal-dev.template > /etc/nginx/sites-available/default
    sed "/$OKTETO_SONAR_HOST$/d" /etc/hosts > /tmp/newhosts
    echo "$OKTETO_SONAR_HOST" >> /tmp/newhosts
    cp /tmp/newhosts /etc/hosts
    rm /tmp/newhosts
fi
