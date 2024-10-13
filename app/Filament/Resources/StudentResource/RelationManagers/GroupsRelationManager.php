<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Filament\Resources\UserResource\Pages\UserGroups;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';
    protected static ?string $title = 'Guruhlar';

    public static function getModelLabel(): string
    {
        return ('guruh'); // Singular form
    }

    public function form(Form $form): Form
    {
        $userGroupsManager = new UserGroups();
        $formSchema = $userGroupsManager->getFormSchema();
        $form->schema($formSchema);
        return $form;
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Yo`nalish'),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('O`qituvchi'),
                Tables\Columns\TextColumn::make('days')
                    ->label('Dars kunlari'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Summa')
                    ->formatStateUsing(fn($state) => format_money($state)),
                Tables\Columns\TextColumn::make('teacher_price_type')
                    ->label('O`qituvchi haqqi turi'),
                Tables\Columns\TextColumn::make('teacher_price')
                    ->label('O`qituvchi haqqi')
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
