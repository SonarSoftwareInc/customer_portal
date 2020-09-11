#!/usr/bin/env bash
set -euo pipefail

if [ $UID != 0 ]; then
    echo This must be run as root.
    exit 1
fi

if ! [ -x "$(command -v docker)" ]; then
    echo "### docker is not installed, installing it now..."
    apt-get update
    apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg-agent \
        software-properties-common
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add -
    add-apt-repository \
       "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"
    apt-get update
    apt-get install -y docker-ce docker-ce-cli containerd.io
fi

if ! [ -x "$(command -v docker-compose)" ]; then
    echo "### docker-compose is not installed, installing it now..."
    curl -L "https://github.com/docker/compose/releases/download/1.23.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

if [ -f .env ]; then
    read -p "### WARNING: Your environment appears to already be set up. Set it up again? [y/N] " -i n -n 1 -r
    echo
    [[ ! $REPLY =~ ^[Yy]$ ]] && exit 1;

    docker-compose stop
    sed -i '/API_PASSWORD=/d' .env

    source .env
fi

if lsof -Pi -sTCP:LISTEN | grep -P ':(80|443)[^0-9]' >/dev/null ; then
    read -p "Port 80 and/or 443 is currently in use. Do you wish to continue anyway? [y/N] " -i n -n 1 -r
    echo
    [[ ! $REPLY =~ ^[Yy]$ ]] && exit 1;
fi

APP_KEY="base64:$(head -c32 /dev/urandom | base64)";
read -ep "Enter your portal domain name (such as portal.example.com): " -i "${NGINX_HOST:-}" NGINX_HOST
read -ep "Enter Your API Username: " -i "${API_USERNAME:-}" API_USERNAME
read -esp "Enter Your API Password (output will not be displayed): " API_PASSWORD
echo
read -ep "Enter Your Instance URL (e.g. https://example.sonar.software): " -i "${SONAR_URL:-}" SONAR_URL
read -ep "Enter your email address: "  -i "${EMAIL_ADDRESS:-}" EMAIL_ADDRESS

cat <<- EOF > ".env"
        APP_KEY=$APP_KEY
        NGINX_HOST=$NGINX_HOST
        API_USERNAME=$API_USERNAME
        API_PASSWORD=$API_PASSWORD
        SONAR_URL=$SONAR_URL
        EMAIL_ADDRESS=$EMAIL_ADDRESS
EOF

export APP_KEY
export NGINX_HOST
export API_USERNAME
export API_PASSWORD
export SONAR_URL
export EMAIL_ADDRESS

docker pull sonarsoftware/customerportal:stable

echo "### Deleting old certificate for $NGINX_HOST ..."
rm -rf ./data/certbot/conf/live/$NGINX_HOST && \
rm -rf ./data/certbot/conf/archive/$NGINX_HOST && \
rm -rf ./data/certbot/conf/renewal/$NGINX_HOST.conf
echo

echo "### Requesting Let's Encrypt certificate for $NGINX_HOST ..."

case "$EMAIL_ADDRESS" in
  "") email_arg="--register-unsafely-without-email" ;;
  *) email_arg="--email $EMAIL_ADDRESS" ;;
esac

docker-compose run --rm \
    -p 80:80 \
    -p 443:443 \
    --entrypoint "\
      certbot certonly --standalone \
        $email_arg \
        -d $NGINX_HOST \
        --rsa-key-size 4096 \
        --agree-tos \
        --force-renewal" certbot
echo

docker-compose up -d

until [ "`docker inspect -f {{.State.Running}} sonar-customerportal`"=="true" ]; do
    sleep 0.1;
done;

echo "### Reconfiguring renewal method to webroot..."

docker-compose run --rm \
    --entrypoint "\
      certbot certonly --webroot \
        -d $NGINX_HOST \
        -w /var/www/certbot \
        --force-renewal" certbot
echo

echo "### The app key is: $APP_KEY";
echo "### Back this up somewhere in case you need it."
docker exec sonar-customerportal sh -c "/etc/my_init.d/99_init_laravel.sh && cd /var/www/html && setuser www-data php artisan sonar:settingskey"
echo "### Navigate to https://$NGINX_HOST/settings and use the above settings key configure your portal."
