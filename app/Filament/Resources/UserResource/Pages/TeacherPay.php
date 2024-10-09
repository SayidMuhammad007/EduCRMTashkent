<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enum\TeacherPriceType;
use App\Filament\Resources\UserResource;
use App\Models\Payment;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

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
                $teacherPrice = $item->studentGroup->price * ($teacherPrice / 100);
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
                            $teacherPrice = $record->studentGroup->price * ($teacherPrice / 100);
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
            ->filters([
                // Filter::make('created_at')
                //     ->form([
                //         DatePicker::make('from')
                //             ->label('Boshlanish sanasi')
                //             ->format('d.m.Y')
                //             ->maxDate(now()),
                //         DatePicker::make('to')
                //             ->label('Tugash sanasi')
                //             ->format('d.m.Y')
                //             ->maxDate(now()),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query->when(
                //             $data['from'] && $data['to'],
                //             fn(Builder $query) => $query->whereBetween('created_at', [
                //                 Carbon::parse($data['from'])->startOfDay(),
                //                 Carbon::parse($data['to'])->endOfDay(),
                //             ])
                //         );
                //     })
            ])
            ->headerActions([
                Action::make('create_payment_debt')
                    ->label('Balansga qo`shish')
                    ->requiresConfirmation()
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
