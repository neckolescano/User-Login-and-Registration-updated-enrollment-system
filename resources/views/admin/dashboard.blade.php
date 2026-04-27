@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-5">
        <h2 style="font-family: 'Orbitron'; color: #800000; font-weight: 700;">ADMIN <span style="color: #d4af37;">DASHBOARD</span></h2>
        <p class="text-muted">Overview of University Enrollment Statistics for 2025-2026.</p>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <a href="{{ route('admin.enrollments.approved') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100 position-relative" style="border-radius: 20px; border-left: 8px solid #2ecc71; background: #fff; transition: transform 0.2s;">
                    <small class="text-muted fw-bold" style="letter-spacing: 1px;">TOTAL ENROLLED</small>
                    <h1 class="display-4 fw-bold mt-2" style="color: #27ae60;">{{ $totalEnrolled }}</h1>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="mb-0 text-muted small">Confirmed academic records</p>
                        <span class="text-success small fw-bold">View List →</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="{{ route('admin.manage_enrollments') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px; border-left: 8px solid #d4af37; background: #fff; transition: transform 0.2s;">
                    <small class="text-muted fw-bold" style="letter-spacing: 1px;">PENDING APPROVALS</small>
                    <h1 class="display-4 fw-bold mt-2" style="color: #856404;">{{ $pendingApprovals }}</h1>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="mb-0 text-muted small">Requires registrar verification</p>
                        <span class="text-warning small fw-bold">Manage →</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold" style="font-family: 'Orbitron'; color: #800000;">Most Popular Courses</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="px-4 py-3">Course Name</th>
                        <th class="px-4 py-3 text-center">Enrolled Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popularCourses as $course)
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ $course->course_name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge rounded-pill bg-light text-dark px-3 py-2 border" style="font-size: 0.9rem;">
                                {{ $course->student_count }} Students
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-4 text-muted">No enrollment data available yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Subtle hover effect for the clickable cards */
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection