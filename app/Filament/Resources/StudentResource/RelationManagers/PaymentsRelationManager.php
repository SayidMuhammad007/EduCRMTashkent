<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enum\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('price')
                    ->label('To`lov summasi')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('$'),
                Select::make('payment_type')
                    ->label('To`lov usuli')
                    ->options(PaymentMethod::class)
                    ->required(),
                Select::make('group_id')
                    ->label('Guruh')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return $this->getOwnerRecord()->groups->pluck('subject.name', 'id');
                    })->required(),
                RichEditor::make('comment')
                    ->label('Izoh')
                    ->helperText('Bu yerga to`lov haqida qo`shimcha malumot yozishingiz mumkin!'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('group.teacher.name'),
                Tables\Columns\TextColumn::make('group.subject.name'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('payment_type'),
                Tables\Columns\TextColumn::make('comment')
                    ->extraAttributes([
                        'style' => 'max-width:260px'
                    ])->html()->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
