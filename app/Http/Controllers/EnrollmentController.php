<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\EnrollmentDetail;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller {

    public function index() {
        $student = Student::where('user_id', auth()->id())->first();
        if (!$student) return redirect()->route('home')->with('error', 'Student profile not found.');

        $enrollments = Enrollment::where('student_id', $student->student_id)
            ->orderBy('created_at', 'desc')->get();

        return view('enrollments.index', compact('enrollments', 'student'));
    }

    public function show($id) {
        $enrollment = Enrollment::findOrFail($id);
        $student = Student::where('user_id', auth()->id())->first();
        
        if ($enrollment->student_id !== $student->student_id) abort(403);

        $details = DB::table('enrollment_details')
            ->join('sections', 'enrollment_details.section_id', '=', 'sections.section_id')
            ->join('subjects', 'sections.subject_id', '=', 'subjects.subject_id')
            ->join('instructors', 'sections.instructor_id', '=', 'instructors.instructor_id')
            ->where('enrollment_details.enrollment_id', $id)
            ->select('subjects.units', 'subjects.subject_name', 'subjects.subject_code', 'sections.*', 'instructors.instructor_name')
            ->get();

        return view('enrollments.show', compact('enrollment', 'details', 'student'));
    }

    /* THE START POINT: STEP 3 (Subject Selection) */
    public function showStep3() {
        $user = Auth::user();
        $student = Student::where('user_id', $user->user_id)->first();

        if (!$student || !$student->course_id) {
            return redirect()->route('home')->with('error', 'Please contact Admin to set up your Course/Profile first.');
        }

        $currentSemester = '1st Semester'; 
        $currentYear = '2025-2026';

        // --- NEW LOGIC: PREVENT MULTIPLE ENROLLMENTS ---
        // Check if student already has a Pending or Approved record for this period
        $existingEnrollment = Enrollment::where('student_id', $student->student_id)
            ->where('semester', $currentSemester)
            ->where('school_year', $currentYear)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();

        if ($existingEnrollment) {
            $msg = ($existingEnrollment->status == 'Pending') 
                ? 'You already have a pending enrollment. Please wait for registrar approval.' 
                : 'You are already officially enrolled for this semester.';
            
            return redirect()->route('enrollments.index')->with('error', $msg);
        }
        // ----------------------------------------------

        session(['enrollment_period' => [
            'course_id' => $student->course_id,
            'semester' => $currentSemester,
            'school_year' => $currentYear
        ]]);

        $sections = Section::whereHas('subject', function($query) use ($student) {
                $query->where('course_id', $student->course_id);
            })
            ->where('semester', $currentSemester)
            ->where('school_year', $currentYear)
            ->with(['subject', 'instructor'])
            ->get();

        return view('enrollments.step3', compact('sections', 'student'));
    }

    public function postStep3(Request $request) {
        $request->validate(['section_ids' => 'required|array|min:1']);

        $totalUnits = DB::table('sections')
            ->join('subjects', 'sections.subject_id', '=', 'subjects.subject_id')
            ->whereIn('sections.section_id', $request->section_ids)
            ->sum('subjects.units');

        if ($totalUnits > 26) {
            return redirect()->back()->with('error', "You cannot exceed 26 units. Current: $totalUnits units.");
        }

        session(['selected_sections' => $request->section_ids]); 
        return redirect()->route('enrollments.step4');
    }

    public function showStep4() {
        $period = session('enrollment_period');
        $section_ids = session('selected_sections');

        if (!$period || !$section_ids) {
            return redirect()->route('enrollments.step3')->with('error', 'Please select subjects first.');
        }

        $selectedSections = Section::with('subject')->whereIn('section_id', $section_ids)->get();
        $student = Student::where('user_id', Auth::user()->user_id)->first();

        return view('enrollments.step4', compact('period', 'selectedSections', 'student'));
    }

    public function store(Request $request) {
        $period = session('enrollment_period'); 
        $section_ids = session('selected_sections');
        $student = Student::where('user_id', Auth::user()->user_id)->first();

        if (!$period || !$section_ids || !$student) {
            return redirect()->route('enrollments.step3')->with('error', 'Session expired.');
        }

        // --- FINAL SAFETY CHECK ---
        $alreadyExists = Enrollment::where('student_id', $student->student_id)
            ->where('semester', $period['semester'])
            ->where('school_year', $period['school_year'])
            ->whereIn('status', ['Pending', 'Approved'])
            ->exists();

        if ($alreadyExists) {
            return redirect()->route('enrollments.index')->with('error', 'An enrollment record for this period already exists.');
        }

        try {
            DB::transaction(function () use ($period, $section_ids, $student) {
                $enrollment = new Enrollment();
                $enrollment->student_id = $student->student_id;
                $enrollment->semester = $period['semester'];
                $enrollment->school_year = $period['school_year'];
                $enrollment->enrollment_date = now();
                $enrollment->status = 'Pending';
                $enrollment->save();

                foreach ($section_ids as $id) {
                    $section = Section::lockForUpdate()->find($id);
                    $currentCount = EnrollmentDetail::where('section_id', $id)->count();

                    if ($currentCount >= $section->capacity) {
                        throw new \Exception("The section " . $section->section_name . " is full.");
                    }

                    $detail = new EnrollmentDetail();
                    $detail->enrollment_id = $enrollment->enrollment_id;
                    $detail->section_id = $id;
                    $detail->save();
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('enrollments.step3')->with('error', $e->getMessage());
        }

        session()->forget(['enrollment_period', 'selected_sections']);
        return redirect()->route('enrollments.success');
    }

    public function success() {
        return view('enrollments.step5');
    }
}