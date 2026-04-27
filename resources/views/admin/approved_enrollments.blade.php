@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-5">
        <h2 style="font-family: 'Orbitron'; color: #800000; font-weight: 700;">APPROVED <span style="color: #d4af37;">ENROLLMENTS</span></h2>
        <p class="text-muted">List of students officially enrolled for the 2025-2026 academic period.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff;">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="font-family: 'Orbitron'; color: #800000;">Confirmed Records</h5>
            <a href="{{ route('dashboard') }}" class="btn btn-sm text-white" style="background-color: #800000; border-radius: 10px;">Back to Dashboard</a>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase" style="letter-spacing: 1px;">
                        <th class="px-4 py-3">Student Name</th>
                        <th class="px-4 py-3">Course & Year</th>
                        <th class="px-4 py-3">SY / Semester</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                    <tr style="transition: background 0.2s;">
                        <td class="px-4 py-3">
                            <span class="fw-bold" style="color: #333;">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-bold small">{{ $enrollment->course_name }}</div>
                            <div class="text-muted small">Year Level: {{ $enrollment->year_level }}</div>
                        </td>
                        <td class="px-4 py-3 small text-muted">
                            {{ $enrollment->school_year }}<br>
                            <span class="fw-bold" style="color: #800000;">{{ $enrollment->semester }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(46, 204, 113, 0.1); color: #27ae60; border: 1px solid #2ecc71; font-size: 0.75rem;">
                                APPROVED
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.records.edit', $enrollment->enrollment_id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   style="border-radius: 8px; font-size: 0.8rem;">
                                   Edit
                                </a>
                                
                                <form action="{{ route('admin.records.destroy', $enrollment->enrollment_id) }}" method="POST" onsubmit="return confirm('WARNING: This will revoke the student\'s official enrollment status. Proceed?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-size: 0.8rem;">
                                        Revoke
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No confirmed enrollment records found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Styling to match your existing dashboard table hover and spacing */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table td {
        border-bottom: 1px solid #f1f1f1;
    }
</style>
@endsection