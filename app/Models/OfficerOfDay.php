<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerOfDay extends Model
{
    use HasFactory;
    
    protected $table = 'officers_of_day';
    
    protected $fillable = [
        'name',
        'type',
        'user_id',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    /**
     * Get the user associated with this officer (if type is 'user')
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Scope for active officers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for custom officers
     */
    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }
    
    /**
     * Scope for user officers
     */
    public function scopeUserType($query)
    {
        return $query->where('type', 'user');
    }
    
    /**
     * Create or update an officer record
     */
    public static function createOrUpdateOfficer($name, $type = 'custom', $userId = null)
    {
        $officer = self::where('name', $name)->where('type', $type)->first();
        
        if ($officer) {
            // Update existing record
            $officer->update([
                'user_id' => $userId,
                'is_active' => true
            ]);
        } else {
            // Create new record
            $officer = self::create([
                'name' => $name,
                'type' => $type,
                'user_id' => $userId,
                'is_active' => true
            ]);
        }
        
        return $officer;
    }
}
