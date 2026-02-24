<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'assessment_id',
        'user_id',
        'action',
        'details',
        'performed_at'
    ];
    
    protected $casts = [
        'performed_at' => 'datetime',
    ];
    
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
