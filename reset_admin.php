<?php
use Modules\Access\Models\Usuario;
use Illuminate\Support\Facades\Hash;

$admin = Usuario::where('email', 'admin@ferre.bo')->first();
if($admin) {
    $admin->password = Hash::make('12345678');
    $admin->save();
    echo "ADMIN PASSWORD RESET TO 12345678\n";
}

$vendedor = Usuario::where('tipoPersona', 'E')->where('email', '!=', 'admin@ferre.bo')->first();
if($vendedor) {
    echo "VENDEDOR: " . $vendedor->email . "\n";
}
