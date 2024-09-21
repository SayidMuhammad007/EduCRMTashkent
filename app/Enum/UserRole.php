<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case MANAGER = 'manager';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::TEACHER => 'O`qituvchi',
            self::MANAGER => 'Manager',
        };
    }
}
