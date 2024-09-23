<?php

namespace App\Filament\Resources\ExpenceResource\Pages;

use App\Filament\Resources\ExpenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpence extends EditRecord
{
    protected static string $resource = ExpenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
