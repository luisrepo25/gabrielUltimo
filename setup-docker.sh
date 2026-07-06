#!/bin/bash

echo "🚀 Iniciando configuración de Docker para el proyecto Ferretería..."

# 1. Copiar .env si no existe
if [ ! -f .env ]; then
    echo "📄 Creando archivo .env a partir de .env.example..."
    cp .env.example .env
fi

# 2. Levantar contenedores
echo "🐳 Levantando contenedores (esto puede tardar unos minutos la primera vez)..."
docker-compose up -d --build

# 3. Instalar dependencias de PHP (Composer)
echo "📦 Instalando dependencias de Composer..."
docker-compose exec app composer install

# 4. Generar App Key
echo "🔑 Generando APP_KEY..."
docker-compose exec app php artisan key:generate

# 5. Instalar dependencias de Node (NPM)
echo "📦 Instalando dependencias de NPM (Node)..."
docker-compose exec app npm install

# 6. Ejecutar migraciones
echo "🗄️ Ejecutando migraciones de la base de datos..."
docker-compose exec app php artisan migrate

echo "✅ ¡Configuración completada! La aplicación debería estar corriendo en http://localhost:8000"
