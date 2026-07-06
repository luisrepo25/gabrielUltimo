<?php

namespace Database\Seeders;

use Modules\Rentals\Models\Maquinaria;
use Illuminate\Database\Seeder;

class MaquinariaSeeder extends Seeder
{
    public function run(): void
    {
        $maquinarias = [
            [
                'codigo' => 'MAQ-001',
                'nombre' => 'Rotomartillo Bosch GBH 2-26 DRE',
                'descripcion' => 'Rotomartillo SDS Plus de 800W con maletín, ideal para perforación en concreto y mampostería.',
                'precio_hora' => 15.00,
                'precio_dia' => 80.00,
                'garantia_sugerida' => 300.00,
                'estado' => 'disponible',
            ],
            [
                'codigo' => 'MAQ-002',
                'nombre' => 'Generador Eléctrico Honda EG6500',
                'descripcion' => 'Generador a gasolina de 6.5 kVA, arranque manual/eléctrico, monofásico 220V.',
                'precio_hora' => 30.00,
                'precio_dia' => 180.00,
                'garantia_sugerida' => 800.00,
                'estado' => 'disponible',
            ],
            [
                'codigo' => 'MAQ-003',
                'nombre' => 'Mezcladora de Concreto 1 Saco Makita',
                'descripcion' => 'Mezcladora de volteo con motor de 1.5 HP a gasolina, capacidad de 350 litros (1 saco).',
                'precio_hora' => 25.00,
                'precio_dia' => 150.00,
                'garantia_sugerida' => 600.00,
                'estado' => 'disponible',
            ],
            [
                'codigo' => 'MAQ-004',
                'nombre' => 'Hidrolavadora Industrial Kärcher HD 5/11 C',
                'descripcion' => 'Hidrolavadora de alta presión de agua fría, flujo de 500 l/h, presión de 110 bar.',
                'precio_hora' => 20.00,
                'precio_dia' => 100.00,
                'garantia_sugerida' => 400.00,
                'estado' => 'disponible',
            ],
            [
                'codigo' => 'MAQ-005',
                'nombre' => 'Cortadora de Azulejos Rubí Speed-62',
                'descripcion' => 'Cortadora manual profesional para azulejos y cerámica con maleta, longitud de corte de 62 cm.',
                'precio_hora' => 10.00,
                'precio_dia' => 50.00,
                'garantia_sugerida' => 200.00,
                'estado' => 'disponible',
            ],
            [
                'codigo' => 'MAQ-006',
                'nombre' => 'Motosierra Stihl MS 250',
                'descripcion' => 'Motosierra de gasolina para mantenimiento de fincas y corte de leña, espada de 40 cm / 16".',
                'precio_hora' => 18.00,
                'precio_dia' => 90.00,
                'garantia_sugerida' => 350.00,
                'estado' => 'disponible',
            ],
        ];

        foreach ($maquinarias as $maq) {
            Maquinaria::updateOrCreate(['codigo' => $maq['codigo']], $maq);
        }
    }
}
