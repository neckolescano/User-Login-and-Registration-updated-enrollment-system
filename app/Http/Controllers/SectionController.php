<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function create()
    {
        // Fix: Join subjects with courses to get department_id for the dynamic instructor filter
        $subjects = DB::table('subjects')
            ->join('courses', 'subjects.course_id', '=', 'courses.course_id')
            ->select('subjects.*', 'courses.department_id')
            ->get();

        $instructors = DB::table('instructors')->get();
        
        return view('admin.sections', compact('subjects', 'instructors'));
    }

    public function store(Request $request)
    {
        // Updated validation to match the database columns from your image
        $request->validate([
            'section_name'  => 'required|string|max:50',
            'subject_id'    => 'required|exists:subjects,subject_id',
            'instructor_id' => 'required|exists:instructors,instructor_id',
            'semester'      => 'required|string',
            'school_year'   => 'required|string',
            'schedule'      => 'required|string|max:255',
            'room'          => 'required|string|max:100',
            'capacity'      => 'required|integer|min:1',
        ]);

        // Fix: Added the missing fields (section_name, semester, school_year) 
        // to match your database schema
        DB::table('sections')->insert([
            'section_name'  => $request->section_name,
            'subject_id'    => $request->subject_id,
            'instructor_id' => $request->instructor_id,
            'semester'      => $request->semester,
            'school_year'   => $request->school_year,
            'schedule'      => $request->schedule,
            'room'          => $request->room,
            'capacity'      => $request->capacity,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('admin.manage_enrollments')->with('success', 'Section/Schedule created successfully!');
    }
}