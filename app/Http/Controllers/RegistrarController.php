<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrarController extends Controller
{
    public function index()
    {
        $pendingCount = Enrollment::where('status', 'Pending')->count();
        $approvedCount = Enrollment::where('status', 'Approved')->count();
        
        $recentEnrollments = Enrollment::with(['student']) 
            ->latest()
            ->take(5)
            ->get();

        return view('registrar.dashboard', compact('pendingCount', 'approvedCount', 'recentEnrollments'));
    }

    public function pending()
    {
        $pendingEnrollments = Enrollment::with(['student.course']) 
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('registrar.pending', compact('pendingEnrollments'));
    }

    /**
     * Updated: Loads the COR (Enrolled Subjects/Sections)
     */
    public function verify($id)
    {
        // Path: Enrollment -> EnrollmentDetail -> Section -> Subject
        $enrollment = Enrollment::with([
            'student.course', 
            'details.section.subject'
        ])->findOrFail($id);

        return view('registrar.verify', compact('enrollment'));
    }

    /**
     * Updated: Simple Approval Logic
     */
    public function approve(Request $request, $id)
    {
        try {
            $enrollment = Enrollment::findOrFail($id);

            // Mark as approved since subjects were already chosen during registration
            $enrollment->update(['status' => 'Approved']);

            return redirect()->route('registrar.pending')->with('success', 'Student enrollment officially approved.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}