<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum TeacherPriceType: string implements HasLabel
{
    case BY_PRICE = 'by_price';
    case BY_PERCENTAGE = 'by_percentage';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BY_PRICE => 'Foiz',
            self::BY_PERCENTAGE => 'Summa',
        };
    }
}
