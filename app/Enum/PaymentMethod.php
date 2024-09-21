<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
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
}
