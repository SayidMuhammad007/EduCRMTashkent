<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enum\Days;
use App\Enum\TeacherPriceType;
use App\Filament\Resources\UserResource\Pages\UserGroups;
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
