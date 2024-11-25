<?php

namespace App\Filament\Pages;

use App\Enum\UserRole;
use App\Filament\Resources\TeacherChartResource\Widgets\TeacherChartWidget;
use Filament\Pages\Page;

class TeacherChart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.teacher-chart';

    protected static ?string $navigationLabel = 'O`qituvchilar hisoboti';

    public static function canAccess(): bool
    {
        return auth()->user()->role_id == UserRole::DIRECTOR;
    }

    public static function getNavigationGroup(): ?string
    {
        return ('Xisobot');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TeacherChartWidget::class,
        ];
    }
}
