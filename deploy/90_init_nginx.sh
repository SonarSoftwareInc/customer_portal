#!/bin/bash
set -euf -o pipefail

envsubst \$NGINX_HOST < /etc/nginx/conf.d/sonar-customerportal.template > /etc/nginx/sites-available/default
