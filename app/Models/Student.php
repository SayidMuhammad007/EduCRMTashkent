<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'birth_date',
        'comment',
        'phone'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function studentDebts(): float
    {
        return $this->debts()->sum('price');
    }
}
