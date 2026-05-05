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
        $totalEnrolled = Enrollment::where('status', 'Approved')->count();
        $pendingApprovals = Enrollment::where('status', 'Pending')->count();

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
            'school_id_number' => 'required|string|unique:students,school_id_number', 
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
                'school_id_number' => $request->school_id_number,
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
    | ENROLLMENT RECORDS (FIXED METHODS)
    |--------------------------------------------------------------------------
    */

    public function allRecords()
    {
        $enrollments = Enrollment::with(['student.course'])
            ->where('status', 'Pending') 
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.manage_enrollments', compact('enrollments'));
    }

    public function approvedRecords()
    {
        $enrollments = Enrollment::with(['student.course'])
            ->where('status', 'Approved') 
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.approved_enrollments', compact('enrollments'));
    }

    public function approve($id)
    {
        $enrollment = Enrollment::find($id);
        if ($enrollment) {
            $enrollment->update(['status' => 'Approved']);
            return redirect()->back()->with('success', 'Enrollment approved successfully!');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function reject($id)
    {
        $enrollment = Enrollment::find($id);
        if ($enrollment) {
            $enrollment->update(['status' => 'Rejected']);
            return redirect()->back()->with('error', 'Enrollment has been rejected.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);
        if ($enrollment) {
            $enrollment->delete(); 
            return redirect()->back()->with('success', 'Enrollment record has been deleted.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    /* |--------------------------------------------------------------------------
    | EDIT & UPDATE RECORD
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $record = Enrollment::with(['student.course', 'details.section.subject'])->findOrFail($id);
        $enrolledSubjects = $record->details;

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

    public function updateRecord(Request $request, $id)
    {
        $request->validate([
            'year_level' => 'required|string',
            'semester' => 'required|string',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        DB::transaction(function () use ($request, $enrollment) {
            // Update enrollment semester
            $enrollment->update([
                'semester' => $request->semester,
            ]);

            // Update student year level
            if ($enrollment->student) {
                $enrollment->student->update([
                    'year_level' => $request->year_level,
                ]);
            }

            // 1. DELETE/DROP SUBJECTS FIRST
            if ($request->has('drop_subjects')) {
                DB::table('enrollment_details')
                    ->whereIn('detail_id', $request->drop_subjects)
                    ->delete();
            }

            // 2. UPDATE REMAINING SECTIONS
            if ($request->has('sections')) {
                foreach ($request->sections as $detail_id => $new_section_id) {
                    // Check if this detail ID was NOT just deleted
                    $wasDropped = $request->has('drop_subjects') && in_array($detail_id, $request->drop_subjects);
                    
                    if (!$wasDropped) {
                        DB::table('enrollment_details')
                            ->where('detail_id', $detail_id)
                            ->update(['section_id' => $new_section_id]);
                    }
                }
            }
        });

        return redirect()->route('admin.manage_enrollments')->with('success', 'Enrollment updated successfully!');
    }
}