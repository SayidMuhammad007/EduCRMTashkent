<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enum\PaymentMethod;
use App\Enum\TeacherPriceType;
use App\Filament\Resources\UserResource;
use App\Models\Payment;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;

class TeacherPay extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.teacher-pay';

    public ?User $record = null;
    public $data = 0;

    public function mount(User $record): void
    {
        $this->record = $record;
        $this->calculateTotal();
    }

    protected function calculateTotal(): void
    {
        $data = Payment::whereHas('studentGroup', function ($query) {
            $query->where('teacher_id', $this->record->id);
        })->where('status', 1)->get();

        $this->data = 0;
        foreach ($data as $item) {
            $teacherPriceType = $item->studentGroup->teacher_price_type;
            $teacherPrice = $item->studentGroup->teacher_price;
            if ($teacherPriceType == TeacherPriceType::BY_PRICE) {
                $this->data += $item->studentGroup->teacher_price;
            } else {
                $teacherPrice = $item->price * ($teacherPrice / 100);
                $this->data += $teacherPrice;
            }
        }
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Payment::whereHas('studentGroup', function ($query) {
                $query->where('teacher_id', $this->record->id);
            })->where('status', 1))
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Talaba')
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
                TextColumn::make('teacher_price')
                    ->label('Hisoblangan summa')
                    ->getStateUsing(function ($record) {
                        $teacherPriceType = $record->studentGroup->teacher_price_type;
                        $teacherPrice = $record->studentGroup->teacher_price;
                        if ($teacherPriceType == TeacherPriceType::BY_PRICE) {
                            return format_money($record->studentGroup->teacher_price);
                        } else {
                            $teacherPrice = $record->price * ($teacherPrice / 100);
                            return format_money($teacherPrice);
                        }
                    })
                    ->sortable(),
                TextColumn::make('teacher_price_type')
                    ->label('Turi')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $teacherPriceType = $record->studentGroup->teacher_price_type;
                        return $teacherPriceType;
                    })
                    ->sortable(),
                TextColumn::make('comment')
                    ->label('Izoh')
                    ->html()
                    ->wrap()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('create_payment_debt')
                    ->label('Balansga qo`shish')
                    ->requiresConfirmation()
                    ->visible(fn() => $this->data > 0)
                    ->action(function () {
                        $this->record->balans += $this->data;
                        $this->record->save();

                        Payment::whereHas('studentGroup', function ($query) {
                            $query->where('teacher_id', $this->record->id);
                        })->where('status', 1)->update(['status' => 0]);

                        $this->calculateTotal();

                        Notification::make()
                            ->title('Balans yangilandi')
                            ->success()
                            ->send();

                        $this->dispatch('refreshPage');
                    }),
                Action::make('pay')
                    ->label('Maosh berish')
                    ->visible(fn() => $this->record->balans > 0)
                    ->form([
                        TextInput::make('balans')
                            ->disabled()
                            ->default(fn() => $this->record->balans),
                        TextInput::make('price')
                            ->label('To`lov summasi')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Select::make('payment_type')
                            ->options(PaymentMethod::class),
                        DatePicker::make('date')
                            ->default(now())
                            ->label('Izoh'),
                        Textarea::make('comment')
                            ->label('Izoh')
                    ])
                    ->action(function (array $data) {
                        $this->record->payments()->create($data);
                        $this->record->balans -= $data['price'];
                        $this->record->save();
                        $this->calculateTotal();

                        Notification::make()
                            ->title('Balans yangilandi')
                            ->success()
                            ->send();

                        $this->dispatch('refreshPage');
                    })
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

    public function getTitle(): string
    {
        return "Talabalar to'lovlari";
    }
}
