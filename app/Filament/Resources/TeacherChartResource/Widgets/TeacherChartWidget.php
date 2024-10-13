<?php

namespace App\Filament\Resources\TeacherChartResource\Widgets;

use App\Models\StudentGroup;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TeacherChartWidget extends ChartWidget
{
    protected static ?string $heading = "Talabalar soni bo'yicha o'qituvchilarning taqsimlanishi";

    public ?string $filter = 'month';

    protected function getData(): array
    {
        $query = StudentGroup::query()
            ->select('teacher_id', DB::raw('COUNT(*) as student_count'))
            ->groupBy('teacher_id')
            ->with('teacher');

        $this->applyDateFilter($query);

        $data = $query->get();

        $colors = $this->generateConsistentColors($data->pluck('teacher_id')->toArray());

        return [
            'datasets' => [
                [
                    'label' => 'Talabalar soni',
                    'data' => $data->pluck('student_count')->toArray(),
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => $data->map(function ($item) use ($colors) {
                return "{$item->teacher->name} ({$item->student_count} ta talaba)";
            })->toArray(),
        ];
    }


    protected function applyDateFilter($query)
    {
        $activeFilter = $this->filter;
        switch ($activeFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Bugun',
            'week' => 'Bu hafta',
            'month' => 'Bu oy',
            'year' => 'Bu yil',
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    private function generateConsistentColors($teacherIds)
    {
        $colors = [];
        foreach ($teacherIds as $teacherId) {
            // Generate a color based on the teacher ID
            $hash = md5('teacher' . $teacherId);
            $colors[$teacherId] = '#' . substr($hash, 0, 6);
        }
        return $colors;
    }
}
