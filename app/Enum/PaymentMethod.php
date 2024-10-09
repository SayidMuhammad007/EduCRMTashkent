<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel, HasColor
{
    case CASH = 'cash';
    case ONLINE = 'online';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => 'Naqd',
            self::ONLINE => 'Karta orqali',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CASH => 'success',
            self::ONLINE => 'primary',
        };
    }
}