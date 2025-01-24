# Running the project locally

This project uses Docker to run the backend and frontend services, you will need to have Docker installed on your machine to run this project.
We also use MySQL, Redis, and Mailhog as services for the backend.

## Prerequisites
* Docker 
* Node.js

## Getting started

Setup the environment file, you can get the secrets from 1password

```shell
copy .env.example .env
```

Start the docker containers, this will take a while the first time you run it

```shell
docker compose up -d 
```

Install the dependencies

```shell
docker compose exec php composer install
```

Run the migrations

```shell
docker compose exec php php artisan migrate
```

Install the frontend dependencies

```shell
npm install
```

Generate the Sonar settings key

```shell
docker compose exec php php artisan sonar:settingskey
```

Head to the [settings page](http://localhost/settings) and enter the generate key then fill up the form with the following values

| Field        | Value                           |
|--------------|---------------------------------|
| Application URL    | https://fibretel.sonar.software |
| Mail Host | admin                           |
| Mail Port | 1025                            |
| Mail Username | admin                           |
| Mail Password | admin                           |
| Sender Email | any email you want              |
| Sender Name | any name you want               |


Roles:
https://fibretel.sonar.software/app#/settings/security/roles/update/4
