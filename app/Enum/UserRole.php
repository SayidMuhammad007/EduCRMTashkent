<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case DIRECTOR = 'director';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::TEACHER => 'O`qituvchi',
            self::DIRECTOR => 'Boshliq',
        };
    }
}
