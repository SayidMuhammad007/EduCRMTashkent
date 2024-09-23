<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enum\AttendanceStatus;
use App\Filament\Resources\UserResource;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentGroup;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Actions\CreateAction;

class StudentData extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.student-data';

    public ?Student $record = null;

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->attendance()->getQuery())
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('student.full_name')
                    ->label('Talaba')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('group.subject.name')
                    ->label('Yo`nalish')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->label('Sana')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Narxi')
                    ->searchable()
                    ->formatStateUsing(fn($state) => format_money($state))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // Add any filters you want here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                CreateAction::make('create_payment_debt')
                    ->label('Yangi qo`shish')
                    ->modalHeading('Davomat')
                    ->visible(function () {
                        return !$this->record->attendance()->where('date', now()->format('Y-m-d'))->first();
                    })
                    ->model(StudentAttendance::class)
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Valyuta')
                            ->options(AttendanceStatus::class)
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('group_id')
                            ->label('Yo`nalish')
                            ->options(function () {
                                return StudentGroup::query()
                                    ->where('student_id', $this->record->id)
                                    ->with('subject:id,name')
                                    ->get()
                                    ->mapWithKeys(function ($group) {
                                        return [$group->id => $group->subject->name];
                                    });
                            })
                            ->native(false)
                            ->required(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $group = StudentGroup::where('id', $data['group_id'])->first();
                        $data['student_id'] = $this->record->id;
                        $data['group_id'] = $group->id;
                        $data['teacher_id'] = $group->teacher_id;
                        $data['price'] = $data['status'] == AttendanceStatus::TRUE->value ? $group->price : 0;
                        $data['date'] = now();
                        return $data;
                    })
                    ->after(function ($record) {
                        $record->status == AttendanceStatus::TRUE ? $record->payments()->create([
                            'student_id' => $record->student_id,
                            'price' => -$record->price,
                        ]) : '';
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTitle(): string
    {
        return "{$this->record->full_name}";
    }
}
