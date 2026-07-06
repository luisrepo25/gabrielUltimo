@echo off
echo Iniciando configuracion de Docker para el proyecto Ferreteria...

if not exist .env (
    echo Creando archivo .env a partir de .env.example...
    copy .env.example .env
)

echo Levantando contenedores (esto puede tardar unos minutos la primera vez)...
docker-compose up -d --build

echo Instalando dependencias de Composer...
docker-compose exec app composer install

echo Generando APP_KEY...
docker-compose exec app php artisan key:generate

echo Instalando dependencias de NPM (Node)...
docker-compose exec app npm install

echo Ejecutando migraciones de la base de datos...
docker-compose exec app php artisan migrate

echo Listo! La aplicacion deberia estar corriendo en http://localhost:8000
pause
