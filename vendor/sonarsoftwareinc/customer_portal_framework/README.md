# Sonar Customer Portal Framework
A framework to build a Sonar integrated customer portal. This library requires Sonar version 0.6.0 or higher to function, and will not work on earlier versions.

## What is this?
This is a PHP library to assist you with building a custom customer portal. This library abstracts a lot of the Sonar API calls and adds convenient shortcuts to many common features required in a customer portal.

## Installing
The recommended installation method is using [Composer](https://www.getcomposer.org). You can install by adding the following to your `composer.json` ...
```
"repositories" [
    ...
    {
        "type": "vcs", 
        "url": "https://github.com/sonarsoftwareinc/customer_portal_framework"
    }
    ...
]
```
and running `composer require sonarsoftwareinc/customer_portal_framework:{VERSION NUMBER}`.

## Configuration
Copy the `.env.example` file from inside the `src` directory to a new file called `.env`. Edit this file and set your Sonar installation URL, API username, and API password. The only permissions required for this library to function are account create, read, update, and delete, and ticket create, read, and update. It is strongly recommended that you create a dedicated account solely for the customer portal - *do not use your admin account!*

## How to use this library
The `Controllers` directory contains controllers that allow you to interface with different aspects of the Sonar API. Each controller function is documented. Some controller functions return the raw data from the Sonar API as a PHP object. Controllers that allow you to save changes back to the Sonar API will return an abstracted object defined in the `Models` directory. Check the format of each controller function to see the requirements.
