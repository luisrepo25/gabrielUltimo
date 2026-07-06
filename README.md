# 🛠️ Ferretería Guisella - Sistema de Gestión

Bienvenido al sistema de gestión de inventario y personal para la **Ferretería Guisella**. Este proyecto está construido con **Laravel 11**, enfocado en una experiencia de usuario premium y una arquitectura sólida basada en roles.

## 🚀 Guía de Instalación para Colaboradores

Si acabas de clonar el repositorio, sigue estos pasos para configurar tu entorno local:

### 1. Instalar dependencias
Asegúrate de tener PHP y Composer instalados. Luego ejecuta:
```bash
composer install
```

### 2. Configurar el archivo de entorno
Copia el archivo de ejemplo y configura tus credenciales de base de datos en el nuevo archivo `.env`:
```bash
cp .env.example .env
```
*Nota: Abre el archivo `.env` y ajusta `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` según tu configuración de MySQL.*

### 3. Generar clave de aplicación
```bash
php artisan key:generate
```

### 4. Ejecutar Migraciones
Crea la estructura de la base de datos:
```bash
php artisan migrate
```

### 5. Poblar datos iniciales (Admin)
Para entrar al sistema necesitas un usuario administrador. Ejecuta el Seeder que hemos preparado:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 6. Iniciar el sistema
```bash
php artisan serve
```

---

## 🔐 Credenciales de Acceso (Prueba)
Una vez configurado, puedes entrar con la cuenta de administrador generada:
- **Email:** `admin@ferre.bo`
- **Password:** `admin123`

## ✨ Características Principales
- **Diseño Premium Luxe**: Interfaz moderna con Glassmorphism y animaciones fluidas.
- **Gestión por Roles**: Vistas diferenciadas para Administradores, Almaceneros y Clientes.
- **Seguridad**: Autenticación integrada con la base de datos de negocio original.
- **Bitácora**: Registro automático de todas las acciones importantes (INSERT, UPDATE, DELETE).