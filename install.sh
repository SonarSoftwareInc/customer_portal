#!/usr/bin/env bash
set -euo pipefail

DEV=false
if [ ${1-""} == "dev" ]; then
  DEV=true
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
    sed -i '/DEV=/d' .env

    source .env
fi

APP_KEY="base64:$(head -c32 /dev/urandom | base64)";
read -ep "Enter your portal domain name (such as portal.example.com): " -i "${NGINX_HOST:-}" NGINX_HOST
read -ep "Enter Your API Username: " -i "${API_USERNAME:-}" API_USERNAME
read -ep "Enter Your API Password: " -i "${API_PASSWORD:-}" API_PASSWORD
read -ep "Enter Your Instance URL (e.g. https://example.sonar.software): " -i "${SONAR_URL:-}" SONAR_URL
read -ep "Enter your email address: "  -i "${EMAIL_ADDRESS:-}" EMAIL_ADDRESS

cat <<- EOF > ".env"
	APP_KEY=$APP_KEY
	NGINX_HOST=$NGINX_HOST
	API_USERNAME=$API_USERNAME
	API_PASSWORD=$API_PASSWORD
	SONAR_URL=$SONAR_URL
	EMAIL_ADDRESS=$EMAIL_ADDRESS
  DEV=$DEV
EOF

export APP_KEY
export NGINX_HOST
export API_USERNAME
export API_PASSWORD
export SONAR_URL
export EMAIL_ADDRESS
export DEV

if [ $DEV == true ]; then
  echo "### Deleting old self-signed certificate for $NGINX_HOST ..."
  rm -rf ./data/certs/*
  echo

  echo "### Creating self-signed certificate for $NGINX_HOST ..."
  mkdir -p ./data/certs

cat > ./data/certs/server-config <<EOF
  # OpenSSL configuration file.
  [ req ]
  prompt = no
  distinguished_name          = req_distinguished_name
  [ req_distinguished_name ]
  C=UA
  ST=Kyiv
  L=Kyiv
  CN=$NGINX_HOST
  O=TheOrganization
  OU=TheOrgUnit
  emailAddress=$EMAIL_ADDRESS
  [ req_ext ]
  subjectAltName = @alt_names
  [alt_names]
  DNS.1   = localhost
EOF

  openssl genrsa -out ./data/certs/nginx-selfsigned.key  2048
  openssl req -sha256 -new -key ./data/certs/nginx-selfsigned.key -out ./data/certs/nginx-selfsigned.csr -config ./data/certs/server-config
  openssl x509 -req -days 825 -in ./data/certs/nginx-selfsigned.csr -signkey ./data/certs/nginx-selfsigned.key -out ./data/certs/nginx-selfsigned.crt -passin "pass:pass"
  rm ./data/certs/nginx-selfsigned.csr
  rm ./data/certs/server-config
  echo

  docker run --rm --interactive --tty --volume $PWD:/app composer:1.8.4 install
  docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
else
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

  docker-compose -f docker-compose.yml -f docker-compose.prod.yml run --rm \
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
  docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

  until [ "`docker inspect -f {{.State.Running}} sonar-customerportal`"=="true" ]; do
    sleep 0.1;
  done;

  echo "### Reconfiguring renewal method to webroot..."

  docker-compose -f docker-compose.yml -f docker-compose.prod.yml run --rm \
      --entrypoint "\
        certbot certonly --webroot \
          -d $NGINX_HOST \
          -w /var/www/certbot \
          --force-renewal" certbot
  echo
fi

echo "### The app key is: $APP_KEY";
echo "### Back this up somewhere in case you need it."
docker exec sonar-customerportal sh -c "/etc/my_init.d/99_init_laravel.sh && cd /var/www/html && setuser www-data php artisan sonar:settingskey"
echo "### Navigate to https://$NGINX_HOST/settings and use the above settings key configure your portal."
