<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class StudentGroup extends Component
{
    protected string $view = 'forms.components.student-group';

    public static function make(): static
    {
        return app(static::class);
    }
}
