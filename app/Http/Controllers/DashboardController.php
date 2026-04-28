<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Check for Administrator
        if ($user && strcasecmp(trim($user->role), 'Administrator') === 0) {
            $totalEnrolled = \DB::table('enrollments')->where('status', 'approved')->count();
            $pendingApprovals = \DB::table('enrollments')->where('status', 'pending')->count();
            
            $popularCourses = \DB::table('courses')
                ->join('students', 'courses.course_id', '=', 'students.course_id')
                ->select('courses.course_name', \DB::raw('count(students.student_id) as student_count'))
                ->groupBy('courses.course_id', 'courses.course_name')
                ->orderBy('student_count', 'desc')
                ->limit(3)
                ->get();

            return view('admin.dashboard', compact('totalEnrolled', 'pendingApprovals', 'popularCourses'));
        }

        // 2. NEW: Check for Registrar Staff
        if ($user && strcasecmp(trim($user->role), 'Registrar Staff') === 0) {
            // Redirect to the specific registrar dashboard route
            return redirect()->route('registrar.dashboard');
        }

        // 3. Default for Students
        return view('home');
    }
}