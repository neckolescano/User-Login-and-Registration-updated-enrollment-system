<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Explicitly define primary key since it is not 'id'
    protected $primaryKey = 'student_id'; 
    
    protected $fillable = [
        'user_id', 
        'course_id', 
        'first_name', 
        'last_name', 
        'year_level', 
        'contact_number'
    ];
    
    /**
     * Accessor to combine first and last name
     * This allows you to use $student->name in your blade files
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'student_id');
    }
}