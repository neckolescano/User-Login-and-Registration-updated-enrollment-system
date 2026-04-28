<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Tell Laravel to use 'user_id' instead of 'id'
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   
        'status', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role Check Helpers
     * Matches the values set in your phpMyAdmin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Administrator';
    }

    public function isRegistrar(): bool
    {
        // Must match "Registrar Staff" exactly as seen in your DB
        return $this->role === 'Registrar Staff';
    }

    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    /**
     * 2. Relationship: A User can be a Student
     * Based on your ERD (1-to-Optional 1)
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }
}