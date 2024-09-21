<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enum\Days;
use App\Enum\TeacherPriceType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->required()
                    ->relationship('subject', 'name')
                    ->native(false)
                    ->searchable()
                    ->live()
                    ->preload(),
                Forms\Components\Select::make('teacher_id')
                    ->required()
                    ->options(function (Get $get) {
                        $subject = $get('subject_id');
                        if ($subject)
                            return User::where('subject_id', $subject)->pluck('name', 'id');
                    })
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('days')
                    ->required()
                    ->options(Days::class)
                    ->multiple()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('price')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                Forms\Components\Select::make('teacher_price_type')
                    ->required()
                    ->options(TeacherPriceType::class)
                    ->live()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('teacher_price')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->suffix(fn(Get $get) => $get('teacher_price_type') == TeacherPriceType::BY_PERCENTAGE->value ? '%' : 'so`m'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name'),
                Tables\Columns\TextColumn::make('teacher.name'),
                Tables\Columns\TextColumn::make('days'),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn($state) => format_money($state)),
                Tables\Columns\TextColumn::make('teacher_price_type'),
                Tables\Columns\TextColumn::make('teacher_price')
                    ->formatStateUsing(fn($state) => format_money($state)),
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