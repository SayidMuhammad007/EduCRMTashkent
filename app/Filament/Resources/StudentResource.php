<?php

namespace App\Filament\Resources;

use App\Enum\PaymentMethod;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Talaba';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Talabalar';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->label('Talaba')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Tug`ilgan sana'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label('Telefon raqami')
                    ->columnSpanFull()
                    ->maxLength(17)
                    ->regex('/^\+998 \d{2} \d{3} \d{2} \d{2}$/')
                    ->placeholder('+998 99 999 99 99')
                    ->mask('+998 99 999 99 99')
                    ->minLength(17),
                Forms\Components\RichEditor::make('comment')
                    ->label('Izoh')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Talaba')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tug`ilgan sana')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon raqami')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Qarzdorlik')
                    ->badge()
                    ->getStateUsing(fn($record) => format_money($record->studentDebts()))
                    ->color(fn($record) => $record->studentDebts() < 0 ? 'danger' : 'success')  // Treat negative debts as danger
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->html()
                    ->label('Izoh')
                    ->wrap()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('pay')
                    ->label('To`lov qilish')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
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
                            ->options(function ($record) {
                                return $record->groups->pluck('subject.name', 'id');
                            })->required(),
                        RichEditor::make('comment')
                            ->label('Izoh')
                            ->required()
                            ->helperText('Bu yerga to`lov haqida qo`shimcha malumot yozishingiz mumkin!'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->payments()->create($data);
                        $record->debts()->create($data);
                    })
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
            RelationManagers\GroupsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
