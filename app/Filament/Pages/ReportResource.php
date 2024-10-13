<?php

namespace App\Filament\Pages;

use App\Models\Report;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;

class ReportResource extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.report-resource';

    protected static ?string $navigationLabel = 'Xisobot';

    public static function getNavigationGroup(): ?string
    {
        return ('Xisobot');
    }

    public $from;
    public $until;

    protected function getTableQuery(): Builder
    {
        return Report::query()->getReportData(
            $this->from,
            $this->until
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('date')
                    ->label('Sana')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_payments')
                    ->label('Talaba to`lovlari')
                    ->money('uzs')
                    ->summarize(Sum::make()
                        ->label('Jami to`lovlar'))
                    ->sortable(),
                TextColumn::make('total_expenses')
                    ->label('Xarajatlar')
                    ->money('uzs')
                    ->summarize(Sum::make()
                        ->label('Jami xarajatlar'))
                    ->sortable(),
                TextColumn::make('total_teacher_payments')
                    ->label('O`qituvchiga to`lovlar')
                    ->money('uzs')
                    ->summarize(Sum::make()
                        ->label('Jami o`qituvchilarga to`lov'))
                    ->sortable(),
                TextColumn::make('net_income')
                    ->label('Kassada')
                    ->money('uzs')
                    ->summarize(Sum::make()
                        ->label('Kassada'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('dan'),
                        DatePicker::make('until')
                            ->label('gacha'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['until'] && $data['from']) {
                            return $query->whereBetween('date', [$data['from'], $data['until']]);
                        } else {
                            return $query;
                        }
                    })
                    ->columns(2)
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['from'] && $data['until']) {
                            return 'Sana filteri: ' . $data['from'] . ' to ' . $data['until'];
                        }
                        return null;
                    })
            ], layout: FiltersLayout::AboveContent)
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->label('Tasdiqlash')
                    ->action(function () {
                        $this->dispatch('refresh');
                    }),
            )
            ->defaultSort('date', 'desc');
    }

    public function getTableRecordKey($record): string
    {
        return $record->date;
    }

    public function getTitle(): string
    {
        return ('Xisobot');
    }
}
