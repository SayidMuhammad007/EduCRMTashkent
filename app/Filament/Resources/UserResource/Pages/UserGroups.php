<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enum\Days;
use App\Enum\TeacherPriceType;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;

class UserGroups extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.user-groups';

    public ?User $record = null;

    public function mount(User $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->groups()->getQuery())
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Talaba')
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Yo`nalish')
                    ->sortable(),
                TextColumn::make('days')
                    ->label('Dars kunlari')
                    ->badge()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Kurs narxi')
                    ->formatStateUsing(fn($state) => format_money($state))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // Add any filters you want here
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(function (Form $form): Form {
                        return $form->schema(self::getFormSchema()); // Use $this instead of self
                    }),
            ])
            ->recordUrl(fn(Model $record) => UserResource::getUrl('history', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // CreateAction::make('create_payment_debt')
                //     ->label('Qarz qo`shish')
                //     ->modalHeading('Yangi qarz qo`shish')
                //     ->model(CompanyPayment::class)
                //     ->color('danger')
                //     ->form(function () {
                //         $remainsManager = new CompanyResource();
                //         return $remainsManager->getFormSchema();
                //     })
                //     ->mutateFormDataUsing(function (array $data): array {
                //         $data['company_id'] = $this->record->id;
                //         $data['user_id'] = auth()->id();
                //         $data['type'] = Type::DEBT;
                //         return $data;
                //     })
            ]);
    }

    public function getTitle(): string
    {
        return "{$this->record->name}";
    }

    public function getFormSchema(): array
    {
        return [
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
        ];
    }
}
