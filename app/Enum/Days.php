<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum Days: string implements HasLabel
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONDAY => 'Dushanba',
            self::TUESDAY => 'Seshanba',
            self::WEDNESDAY => 'Chorshanba',
            self::THURSDAY => 'Payshanba',
            self::FRIDAY => 'Juma',
            self::SATURDAY => 'Shanba',
            self::SUNDAY => 'Yakshanba',
        };
    }
}
