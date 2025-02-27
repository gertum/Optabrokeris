# Opta broker

# installation

## docker install (ubuntu)

https://docs.docker.com/engine/install/ubuntu/

    sudo apt-get remove docker docker-engine docker.io containerd runc

    sudo apt-get update

    sudo apt-get install \
    ca-certificates \
    curl \
    gnupg \
    lsb-release
    
    sudo mkdir -p /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

    echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
    $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    
    sudo apt-get update
    sudo apt-get install docker-ce docker-ce-cli containerd.io docker-compose-plugin

## Manual installation tasks 

Make environment settings file

    cp .env.example .env
    cp xdebug.ini.example xdebug.ini

Docker network:

    docker network create optabroker-network

### if need to rebuild docker

    docker build -t inkodus/optabroker .docker/optabroker/local

## running

With user rights

    docker exec -itu 1000:1000 optabroker bash

With root rights

    docker exec -it optabroker bash

Change to full access to the cache and logs files 

    chmod 777 -R storage

Must start composer
    
    composer install

## xdebug

Must write correct ip address in to xdebug.ini and restart docker container.

To get your container ip address you may write from outside you container:

    docker inspect optabroker

Activization for cmd line:

    export XDEBUG_SESSION=PHPSTORM
    export PHP_IDE_CONFIG="serverName=optabroker.dv"


When you debug from insomnia or postman, add  a header: Cookie XDEBUG_SESSION=PHPSTORM

# application

## migrations

    php artisan migrate

( php artisan migrate:refresh first time)

## initial user

Run this command only in dev environment.

    php artisan db:seed AdminUserSeeder

now you will be able to login with admin@inkodus.lt:Labas123

## Accessing login page for the first time:

    http://localhost:8080/login

## npm install and build libs

    npm install 
    npm run build

## permissions

When you get message about permissions, run the following command from inside 'optabroker' container (with root : docker exec -it optabroker). 

    chmod -R 777 storage 
    
Should solve this by using docker / (docker compose) settings where we will work with a custom user permissions instead of the root.

## key

In case you get error : "Illuminate\Encryption\MissingAppKeyException: No application encryption key has been specified."
Generate your application encryption key using 
    
    php artisan key:generate
.

## API endpoints 

Upload:
/api/job/1/upload

multipart file parameter 'task'

Download:
/api/job/1/donwload

### login
Request:

    http://localhost:8080/login

Body:

    {"email":"admin@inkodus.lt","password":"Labas123","remember":false}

Response:

    vJt3JqBkzqTUkPbZS9FTZKW6aT8h3lJjQifPQcdS


Others http request headers will need to have:

    X-CSRF-TOKEN : vJt3JqBkzqTUkPbZS9FTZKW6aT8h3lJjQifPQcdS

Or a http request parameter will need to be passed: 
    
    _token=vJt3JqBkzqTUkPbZS9FTZKW6aT8h3lJjQifPQcdS


### adding job

POST 

    http://localhost:8080/api/job?type=roster

body 

    {
    "name" : "kapiec"
    }

arba vietoj body galima paduoti "name" per parametrus : &name=kapiec

### job list

http://localhost:8080/api/job



### uploading

POST 

    http://localhost:8080/api/job/1/upload

Body

    multipart ,
    give parameter 'file' value - your file.


# tests

## unit

    vendor/bin/phpunit

## unit slow

    vendor/bin/phpunit --testsuite=UnitSlow


