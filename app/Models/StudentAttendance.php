<?php

namespace App\Models;

use App\Enum\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAttendance extends Model
{
    use HasFactory;

    protected  $fillable = [
        'student_id',
        'teacher_id',
        'group_id',
        'status',
        'price',
        'date',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }
}
