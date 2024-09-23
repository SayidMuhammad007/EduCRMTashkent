<?php

namespace App\Filament\Resources\ExpenceResource\Pages;

use App\Filament\Resources\ExpenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpences extends ListRecords
{
    protected static string $resource = ExpenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
