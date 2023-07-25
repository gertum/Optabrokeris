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

    docker build -t inkodus/optabroker .docker/optabroker

## running

With user rights

    docker exec -itu 1000:1000 optabroker bash

With root rights

    docker exec -it optabroker bash

Change to full access to the cache and logs files 

    chmod 777 -R storage

## xdebug

Must write correct ip address in to xdebug.ini and restart docker container.

To get your container ip address you may write from outside you container:

    docker inspect optabroker

Activization for cmd line:

    export XDEBUG_SESSION=PHPSTORM
    export PHP_IDE_CONFIG="serverName=optabroker.dv"


# application

# migrations

    php artisan migrate

# npm install and build libs

    npm install 
    npm run build

# permissions

When you get message about permissions, run the following command from inside 'optabroker' container (with root : docker exec -it optabroker). 

    chmod -R 777 storage 
    
Should solve this by using docker / (docker compose) settings where we will work with a custom user permissions instead of the root.

