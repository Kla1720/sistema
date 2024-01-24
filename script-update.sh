#!/bin/bash

BRANCH=${1:-'master'}
SERVICE_NUMBER=${2:-'1'}

git pull origin "$BRANCH"

docker-compose exec -T fpm$SERVICE_NUMBER composer install
docker-compose exec -T fpm$SERVICE_NUMBER composer require zanysoft/laravel-zip
docker-compose exec -T fpm$SERVICE_NUMBER php artisan migrate
docker-compose exec -T fpm$SERVICE_NUMBER php artisan tenancy:migrate
docker-compose exec -T fpm$SERVICE_NUMBER php artisan cache:clear
docker-compose exec -T fpm$SERVICE_NUMBER php artisan config:cache
docker-compose exec -T fpm$SERVICE_NUMBER php artisan view:clear
docker-compose exec -T fpm$SERVICE_NUMBER chmod -R 777 vendor/mpdf/mpdf

#nota habilitar extension extension=intl en docker está en el php.ini del contenedor php-fpm
#en el contenedror php-fpm ejecutar
    apt-get update -y
    apt-get install -y php7.2-intl

    nota: mantener los archivos de configuración, seleccionar 2 y luego N
#luego reiniciar nginx en el contenedr nginx con
    service nginx restart

# ejecutar en contenedor php-fpm
    composer require mike42/escpos-php

# si dá error al compilar ejecutar npm install

# ejecutar
npm i vuedraggable
npm i --save v-movable

# Actualizacion de 3 a 4
# acciones para actualizar correctamente
1.- ejecutar composer install
 instalará todas als dependencias, si falta algo ejecutar
 composer require mckenziearts/laravel-notify 1.*

2.- ejecutar las migraciones

 php artisan migrate
 php artisan tenancy:migrate --path=database/migrations/tenant/2021_03_9_123548_tenant_fix_module_to_modules.php
 php artisan tenancy:migrate

3.-  npm install vuex --save
	esto solo en la pc de desarrollo, en el vps, no




