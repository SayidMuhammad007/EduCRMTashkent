<?php

namespace App\Models;

use App\Enum\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'price',
        'comment',
        'date',
        'payment_type'
    ];

    protected $casts = [
        'payment_type' => PaymentMethod::class
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
