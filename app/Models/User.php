<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_FRONT_DESK = 'front_desk';
    const ROLE_SECTION_STAFF = 'section_staff';
    const ROLE_SECTION_OFFICER = 'section_officer';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'assigned_category_id',
        'is_active',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category assigned to this user
     */
    public function assignedCategory()
    {
        return $this->belongsTo(Category::class, 'assigned_category_id');
    }

    /**
     * Get inquiries served by this user
     */
    public function servedInquiries()
    {
        return $this->hasMany(Inquiry::class, 'served_by');
    }

    /**
     * Get assessments processed by this user
     */
    public function processedAssessments()
    {
        return $this->hasMany(Assessment::class, 'processed_by');
    }

    /**
     * Check if user is front desk
     */
    public function isFrontDesk(): bool
    {
        return $this->role === self::ROLE_FRONT_DESK;
    }

    /**
     * Check if user is section staff
     */
    public function isSectionStaff(): bool
    {
        return $this->role === self::ROLE_SECTION_STAFF;
    }

    /**
     * Check if user is section officer
     */
    public function isSectionOfficer(): bool
    {
        return $this->role === self::ROLE_SECTION_OFFICER;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get the username for authentication
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
