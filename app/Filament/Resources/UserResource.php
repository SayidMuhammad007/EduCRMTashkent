<?php

namespace App\Filament\Resources;

use App\Enum\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Xodim';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Xodimlar';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ism')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(table: 'users', column: 'email', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Parol')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role_id')
                    ->label('Rol')
                    ->options(UserRole::class)
                    ->native(false)
                    ->required()
                    ->live(debounce: 500),
                Forms\Components\Select::make('subject_id')
                    ->label('Yo`nalish')
                    ->relationship('subject', 'name')
                    ->required(fn(Get $get) => $get('role_id') == UserRole::TEACHER->value)
                    ->visible(fn(Get $get) => $get('role_id') == UserRole::TEACHER->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ism')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role_id')
                    ->label('Rol')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Yo`nalish')
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
                SelectFilter::make('role_id')
                    ->native(false)
                    ->options(UserRole::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(fn(Model $record) => $record->role_id == UserRole::TEACHER ? UserResource::getUrl('students', ['record' => $record]) : '')
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
            'students' => Pages\UserGroups::route('/{record}/students'),
            'history' => Pages\StudentData::route('/student/{record}/history'),
        ];
    }
}
