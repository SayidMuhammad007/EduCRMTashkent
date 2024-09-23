<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasLabel, HasColor
{
    case TRUE = 'true';
    case FALSE = 'false';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TRUE => 'Keldi',
            self::FALSE => 'Kelmadi',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FALSE => 'danger',
            self::TRUE => 'success',
        };
    }
}
