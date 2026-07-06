<?php
declare(strict_types=1);

namespace Modules\Access\Core\DTOs;

final class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false
    ) {}
}
