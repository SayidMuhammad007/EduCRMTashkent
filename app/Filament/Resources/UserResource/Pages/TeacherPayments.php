<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enum\UserRole;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\Page;
use App\Models\TeacherPayment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeacherPayments extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.teacher-payments';

    public ?User $record = null;

   

    public function mount(User $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TeacherPayment::where('teacher_id', $this->record->id))
            ->columns([
                TextColumn::make('date')
                    ->label('Sana')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Summa')
                    ->money('uzs')
                    ->sortable(),
                TextColumn::make('payment_type')
                    ->label('To`lov turi')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            // ->headerActions([
            //     Action::make('create_payment_debt')
            //         ->label('Balansga qo`shish')
            //         ->requiresConfirmation()
            //         ->visible(fn() => $this->data > 0)
            //         ->action(function () {
            //             $this->record->balans += $this->data;
            //             $this->record->save();

            //             Payment::whereHas('studentGroup', function ($query) {
            //                 $query->where('teacher_id', $this->record->id);
            //             })->where('status', 1)->update(['status' => 0]);

            //             $this->calculateTotal();

            //             Notification::make()
            //                 ->title('Balans yangilandi')
            //                 ->success()
            //                 ->send();

            //             $this->dispatch('refreshPage');
            //         }),
            //     Action::make('pay')
            //         ->label('Maosh berish')
            //         ->visible(fn() => $this->record->balans > 0)
            //         ->form([
            //             TextInput::make('balans')
            //                 ->disabled()
            //                 ->default(fn() => $this->record->balans),
            //             TextInput::make('price')
            //                 ->label('To`lov summasi')
            //                 ->mask(RawJs::make('$money($input)'))
            //                 ->stripCharacters(','),
            //             Textarea::make('comment')
            //                 ->label('Izoh')
            //         ])
            //         ->action(function (array $data) {
            //             $this->record->payments($data);
            //             $this->record->balans -= $data['price'];
            //             $this->record->save();
            //             $this->calculateTotal();

            //             Notification::make()
            //                 ->title('Balans yangilandi')
            //                 ->success()
            //                 ->send();

            //             $this->dispatch('refreshPage');
            //         })
            // ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $record->teacher->balans += $record->price;
                        $record->teacher->save();
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function getTitle(): string
    {
        return "Maosh tarixi";
    }
}
