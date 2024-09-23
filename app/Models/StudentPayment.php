<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_attendance_id',
        'price',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function studentAttendance(): BelongsTo
    {
        return $this->belongsTo(StudentAttendance::class);
    }
}
