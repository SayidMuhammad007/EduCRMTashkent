<?php

namespace App\Models;

use App\Enum\Days;
use App\Enum\TeacherPriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'days',
        'price',
        'teacher_price_type',
        'teacher_price',
    ];

    protected $casts = [
        'teacher_price_type' => TeacherPriceType::class,
        'days' => 'array',
    ];

    public function getDaysAttribute($value)
    {
        $array = is_array($value) ? $value : json_decode($value, true);
        return array_map(fn($day) => Days::from($day), $array);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
