<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenceResource\Pages;
use App\Models\Expence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenceResource extends Resource
{
    protected static ?string $model = Expence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Xarajat';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Xarajatlar';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->label('Summa')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->label('Sana')
                    ->default(now())
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->label('Izoh')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    ->label('Summa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Sana')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Izoh')
                    ->wrap()
                    ->html()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpences::route('/'),
            // 'create' => Pages\CreateExpence::route('/create'),
            // 'edit' => Pages\EditExpence::route('/{record}/edit'),
        ];
    }
}
