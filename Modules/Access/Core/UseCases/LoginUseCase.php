<?php
declare(strict_types=1);

namespace Modules\Access\Core\UseCases;

use Illuminate\Support\Facades\Auth;
use Modules\Access\Core\DTOs\LoginDTO;
use Modules\Audit\Models\Bitacora;

final class LoginUseCase
{
    /**
     * Intenta autenticar al usuario y registrar la actividad en la bitácora
     */
    public function execute(LoginDTO $dto): bool
    {
        if (Auth::attempt(['email' => $dto->email, 'password' => $dto->password], $dto->remember)) {
            $user = Auth::user();
            Bitacora::registrar('Login', 'usuario', $user->ci ?? $user->id, 'Inicio de sesión exitoso');
            return true;
        }

        return false;
    }
}
