[![Customer Portal](https://i.imgur.com/AMoOuyg.png)](https://github.com/SonarSoftwareInc/customer_portal)

# Sonar Customer Portal

This is a prebuilt and self-hosted customer portal for [Sonar](https://sonar.software).

## Quick start

These instructions will get you set up and running with SSL through [LetsEncrypt](https://letsencrypt.org) as well as automatic updates provided by [Watchtower](https://github.com/v2tec/watchtower).

**_If you are a current Sonar customer, and you need assistance with any part of this process, please don't hesitate to reach out to support@sonar.software for help. We are more than happy to help you get your portal setup!_**

You'll need a machine running Ubuntu 18 or 22 x64. **Please note that the customer portal will not work Ubuntu 19, as Docker is currently unsupported.** The installation script currently assumes a Debian-based distro. We recommend a minimum of 2 vCPUs, at least 2GB of RAM, and at least 25 GB of storage.

It will need a public facing IP address and a valid domain name pointing to it (e.g. portal.myisp.com).

## Getting started

SSH into your VM.

Install required packages:
`sudo apt-get -y update && sudo apt-get -y upgrade && sudo apt-get -y install git unzip && curl https://get.docker.com/ | bash -`

Clone the repository:
`git clone https://github.com/SonarSoftwareInc/customer_portal.git`

Now change directory into the repository that we've just cloned:
`cd customer_portal`

Run the install script:
`sudo ./install.sh | tee customerportal-install.log`

Follow the instructions as prompted by the installation script.
You can view the installation log by running
`cat customerportal-install.log`

### Define a Role

#### SonarV1 Role Instructions ####
1. Go to System > Roles
1. Create a new role called Customer Portal
1. Assign the following permissions:
   * Accounts: Read, Create, Update, Delete
   * Financial: Read
   * Ticketing: Read, Create, Update, Delete
   * Ticket Super User: Enabled

#### SonarV2 Role Instructions ####

The Sonar version 2 instructions have been moved to the [Sonar Knowledge Base](https://docs.sonar.expert/baseline-config/customer-portal-configuration-checklist#api_user_permissions).

### Define a Username

The API username and API password you are prompted for are credentials for your Sonar instance. You should create a dedicated user to utilize for the customer portal - **do not use your admin username/password!** Create the user as instructed above and assign to this "Customer Portal" role.

**Please be aware that at this time, you cannot use a `^` or `\` character in the API password. This is a known issue that may be addressed in the future.**

## Post-Installation Setup

After the setup process is complete, your instance should be up and running. You can navigate to the settings URL (which is `/settings` on the domain you setup, e.g. `https://portal.myisp.com/settings`) and use the settings key that should have been generated for you with the installation script.

## Common tasks

Starting the customer portal:
`sudo docker-compose start`

Stopping the customer portal:
`sudo docker-compose stop`

Viewing the logs:
`sudo docker-compose logs`

## Commands you can use after setup

From the `customer_portal` directory, you can execute `sudo docker-compose exec app /bin/bash` to access the docker container that the portal is running in. After doing this, you can execute the commands below.

* `php artisan sonar:settingskey` will generate a new key for the `/settings` page if you forget the one you had.
* `php artisan sonar:test:smtp {email}` will test your email configuration. Replace `{email}` with your email address, and the portal will attempt to send you a test email.
* `php artisan sonar:test:paypal` will test your PayPal configuration, if it is enabled.

## Upgrading

Upgrades for the customer portal are done automatically and require no interaction on your part! The customer portal will automatically check for updates every 5 minutes and update itself. For further customization such as setting an update window or configuring email and/or Slack notifications, please see https://github.com/v2tec/watchtower

## Troubleshooting

If you get the following error during setup:

```
[/var/www/html/storage]:rw': invalid mount config for type "volume": invalid mount path: '[/var/www/html/storage]' mount path must be absolute
```

Try removing the created storage volume by executing `sudo docker volume rm customer_portal_storage` and rerunning the installation script.

## Customizing the portal

This portal is built using [Laravel](https://laravel.com/). You are welcome to fork and modify this repository for your own needs! Do not attempt to customize the files inside the existing Docker container, as they will be automatically overwritten during upgrade. If you need help customizing this portal beyond what is currently available, we recommend [Solutions4Ebiz](https://www.solutions4ebiz.com/) & [SBRConsulting](https://www.sbrconsulting-llc.com/) as experienced third party developers.
