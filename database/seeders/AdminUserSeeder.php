<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Crear los Roles básicos si no existen (Evita error de llave foránea)
        \Modules\Access\Models\Rol::updateOrCreate(['id' => 1], ['nombre' => 'Administrador', 'descripcion' => 'Control total del sistema']);
        \Modules\Access\Models\Rol::updateOrCreate(['id' => 2], ['nombre' => 'Almacenero', 'descripcion' => 'Gestión de inventario y productos']);
        \Modules\Access\Models\Rol::updateOrCreate(['id' => 3], ['nombre' => 'Cliente', 'descripcion' => 'Acceso al catálogo y pedidos']);

        // 1. Crear el Usuario base
        $admin = \Modules\Access\Models\Usuario::updateOrCreate(
            ['ci' => 1234567],
            [
                'nombre' => 'Administrador',
                'apellido' => 'General',
                'telefono' => 70000000,
                'sexo' => 'M',
                'email' => 'admin@ferre.bo',
                'domicilio' => 'Central Ferretería',
                'tipoPersona' => 'E', // Empleado
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            ]
        );

        // 2. Registrarlo como Empleado
        \Modules\Access\Models\Empleado::updateOrCreate(
            ['ci' => $admin->ci],
            [
                'salario' => 5000.00,
                'estado' => 'Activo'
            ]
        );

        // 3. Asignarle el ROL de Administrador
        \Modules\Access\Models\EstadoRol::updateOrCreate(
            ['id_rol' => 1, 'ci_empleado' => $admin->ci],
            ['fechaInicio' => now(), 'estado' => 'Activo']
        );
    }
}
