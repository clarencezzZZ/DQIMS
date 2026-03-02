<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_month',
        'current_value',
    ];

    /**
     * Get the next assessment number for the given year and month
     */
    public static function getNextNumber(string $year, string $month): string
    {
        $yearMonth = $year . '-' . $month;
        
        return \DB::transaction(function () use ($yearMonth) {
            // Try to find existing sequence record
            $sequence = self::where('year_month', $yearMonth)->lockForUpdate()->first();
            
            if ($sequence) {
                // Increment the current value
                $sequence->increment('current_value');
                $nextNumber = $sequence->current_value;
            } else {
                // Create new sequence record starting with 1
                $sequence = self::create([
                    'year_month' => $yearMonth,
                    'current_value' => 1,
                ]);
                $nextNumber = 1;
            }
            
            return $yearMonth . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}