<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Report extends Model
{
    protected $table = 'reports';  // This is a dummy table name, as we're using a custom query

    public function scopeGetReportData($query, $fromDate = null, $untilDate = null)
    {
        $currentDate = Carbon::create(2024, 10, 12);

        $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : $currentDate->copy()->subDays(30)->startOfDay();
        $untilDate = $untilDate ? Carbon::parse($untilDate)->endOfDay() : $currentDate->copy()->endOfDay();
        $subQuery = DB::table('payments')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price) as total_payments'),
                DB::raw('0 as total_expenses'),
                DB::raw('0 as total_teacher_payments')
            )
            ->whereBetween('created_at', [$fromDate, $untilDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->unionAll(
                DB::table('expences')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('0 as total_payments'),
                        DB::raw('SUM(price) as total_expenses'),
                        DB::raw('0 as total_teacher_payments')
                    )
                    ->whereBetween('created_at', [$fromDate, $untilDate])
                    ->groupBy(DB::raw('DATE(created_at)'))
            )
            ->unionAll(
                DB::table('teacher_payments')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('0 as total_payments'),
                        DB::raw('0 as total_expenses'),
                        DB::raw('SUM(price) as total_teacher_payments')
                    )
                    ->whereBetween('created_at', [$fromDate, $untilDate])
                    ->groupBy(DB::raw('DATE(created_at)'))
            );

        return $query->fromSub($subQuery, 'combined_data')
            ->select(
                'date',
                DB::raw('SUM(total_payments) as total_payments'),
                DB::raw('SUM(total_expenses) as total_expenses'),
                DB::raw('SUM(total_teacher_payments) as total_teacher_payments'),
                DB::raw('SUM(total_payments) - SUM(total_expenses) - SUM(total_teacher_payments) as net_income')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc');
    }
}