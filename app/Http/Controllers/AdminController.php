<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /* |--------------------------------------------------------------------------
    | ADMIN DASHBOARD STATISTICS
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $totalEnrolled = DB::table('enrollments')->where('status', 'approved')->count();
        $pendingApprovals = DB::table('enrollments')->where('status', 'pending')->count();

        $popularCourses = DB::table('courses')
            ->join('students', 'courses.course_id', '=', 'students.course_id')
            ->select('courses.course_name', DB::raw('count(students.student_id) as student_count'))
            ->groupBy('courses.course_id', 'courses.course_name')
            ->orderBy('student_count', 'desc')
            ->limit(3)
            ->get();

        return view('admin.dashboard', compact('totalEnrolled', 'pendingApprovals', 'popularCourses'));
    }

    /* |--------------------------------------------------------------------------
    | SUBJECT & ACADEMIC MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $courses = DB::table('courses')->get(); 
        return view('admin.add_subject', compact('courses'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'course_id'    => 'required',
            'subject_code' => 'required|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'units'        => 'required|integer|min:1|max:5',
        ]);

        $subject = new Subject();
        $subject->course_id = $request->course_id;
        $subject->subject_code = strtoupper($request->subject_code);
        $subject->subject_name = $request->subject_name;
        $subject->units = $request->units;
        $subject->save();

        Section::create([
            'subject_id'    => $subject->subject_id, 
            'instructor_id' => 1, 
            'semester'      => '1st Semester',
            'school_year'   => '2025-2026',
            'schedule'      => 'TBA',
            'capacity'      => 40
        ]);

        return redirect()->back()->with('success', 'Subject and default Section created!');
    }

    /* |--------------------------------------------------------------------------
    | USER / STUDENT MANAGEMENT (Admin Only)
    |--------------------------------------------------------------------------
    */
    public function createUser()
    {
        $departments = DB::table('departments')->get();
        $courses = DB::table('courses')->get();
        return view('admin.register_student', compact('departments', 'courses'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'contact_number' => 'required|string',
            'course_id' => 'required|exists:courses,course_id',
            'year_level' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Student', 
            ]);

            DB::table('students')->insert([
                'user_id' => $user->user_id,
                'course_id' => $request->course_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'year_level' => $request->year_level,
                'contact_number' => $request->contact_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Student account and profile created successfully!');
    }

    /* |--------------------------------------------------------------------------
    | ENROLLMENT VERIFICATION & APPROVAL
    |--------------------------------------------------------------------------
    */
   public function allRecords()
    {
        $enrollments = DB::table('enrollments')
            ->join('students', 'enrollments.student_id', '=', 'students.student_id')
            ->join('courses', 'students.course_id', '=', 'courses.course_id')
            ->where('enrollments.status', 'pending') 
            ->select(
                'enrollments.*', 
                'students.first_name', 
                'students.last_name', 
                'students.year_level',
                'courses.course_name'
            )
            ->orderBy('enrollments.created_at', 'desc')
            ->get();

        return view('admin.manage_enrollments', compact('enrollments'));
    }

   public function approve($id)
    {
        $enrollment = DB::table('enrollments')->where('enrollment_id', $id)->first();

        if ($enrollment) {
            DB::table('enrollments')
                ->where('enrollment_id', $id)
                ->update(['status' => 'approved']);

            return redirect()->back()->with('success', 'Enrollment approved successfully!');
        }

        return redirect()->back()->with('error', 'Record not found.');
    }

    public function approvedRecords()
    {
    $enrollments = DB::table('enrollments')
        ->join('students', 'enrollments.student_id', '=', 'students.student_id')
        ->join('courses', 'students.course_id', '=', 'courses.course_id')
        ->where('enrollments.status', 'approved') 
        ->select('enrollments.*', 'students.first_name', 'students.last_name', 'students.year_level', 'courses.course_name')
        ->orderBy('enrollments.updated_at', 'desc') // Show most recently approved first
        ->get();

    return view('admin.approved_enrollments', compact('enrollments'));
    }

    public function reject($id)
    {
        DB::table('enrollments')
            ->where('enrollment_id', $id)
            ->update(['status' => 'rejected']);

        return redirect()->back()->with('error', 'Enrollment has been rejected.');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);
        if ($enrollment) {
            $enrollment->delete(); // This removes the record, allowing the student to re-enroll
            return redirect()->back()->with('success', 'Enrollment record has been deleted.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function edit($id)
    {
        $record = DB::table('enrollments')
            ->join('students', 'enrollments.student_id', '=', 'students.student_id')
            ->where('enrollment_id', $id)
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        $enrolledSubjects = DB::table('enrollment_details')
            ->join('sections', 'enrollment_details.section_id', '=', 'sections.section_id')
            ->join('subjects', 'sections.subject_id', '=', 'subjects.subject_id')
            ->where('enrollment_details.enrollment_id', $id)
            ->select(
                'enrollment_details.detail_id', 
                'subjects.subject_name', 
                'subjects.subject_code', 
                'subjects.subject_id', // ADDED THIS LINE
                'sections.schedule', 
                'sections.section_id'
            )
            ->get();

        $allSections = DB::table('sections')
        ->join('subjects', 'sections.subject_id', '=', 'subjects.subject_id')
        ->leftJoin('enrollment_details', 'sections.section_id', '=', 'enrollment_details.section_id')
        ->select(
            'sections.section_id',
            'sections.subject_id',
            'sections.schedule',
            'sections.capacity',
            'subjects.subject_code',
            'subjects.subject_name',
            DB::raw('sections.capacity - COUNT(enrollment_details.detail_id) as remaining_slots')
        )
        ->groupBy(
            'sections.section_id', 
            'sections.subject_id', 
            'sections.schedule', 
            'sections.capacity', 
            'subjects.subject_code', 
            'subjects.subject_name'
        )
        ->get();

        return view('admin.edit_enrollment', compact('record', 'enrolledSubjects', 'allSections'));
    }
    // NEW: Handles the form submission from the edit page
    public function updateRecord(Request $request, $id)
    {
        $request->validate([
            'year_level' => 'required|string',
            'semester' => 'required|string',
        ]);

        $enrollment = DB::table('enrollments')->where('enrollment_id', $id)->first();

        if ($enrollment) {
            // Update the enrollment
            DB::table('enrollments')
                ->where('enrollment_id', $id)
                ->update([
                    'semester' => $request->semester,
                    'updated_at' => now()
                ]);

            // Update the student profile
            DB::table('students')
                ->where('student_id', $enrollment->student_id)
                ->update([
                    'year_level' => $request->year_level,
                    'updated_at' => now()
                ]);

            // Handle Section/Schedule Changes if any (Looping through provided sections)
            if ($request->has('sections')) {
                foreach ($request->sections as $detail_id => $new_section_id) {
                    DB::table('enrollment_details')
                        ->where('detail_id', $detail_id)
                        ->update(['section_id' => $new_section_id]);
                }
            }

            return redirect()->route('admin.manage_enrollments')->with('success', 'Enrollment updated successfully!');
        }

        return redirect()->back()->with('error', 'Record not found.');
    }


}