<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_amount',
        'is_active',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
