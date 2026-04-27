@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <h2 style="font-family: 'Orbitron'; color: #800000; font-weight: 700;">
                PENDING <span style="color: #d4af37;">ENROLLMENTS</span>
            </h2>
            <p class="text-muted mb-0">Review and verify student enrollment requests for the current period.</p>
        </div>
        <a href="{{ route('admin.enrollments.approved') }}" class="btn btn-sm text-white" style="background-color: #800000; border-radius: 10px; font-family: 'Orbitron';">
            View Approved List →
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #fff;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold" style="font-family: 'Orbitron'; color: #800000;">Enrollment Requests</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase" style="letter-spacing: 1px;">
                        <th class="px-4 py-3">Student Name</th>
                        <th class="px-4 py-3">Course & Year</th>
                        <th class="px-4 py-3">Date Submitted</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $record)
                    <tr style="transition: background 0.2s;">
                        <td class="px-4 py-3 fw-bold" style="color: #333;">
                            {{ $record->first_name }} {{ $record->last_name }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-bold small">{{ $record->course_name }}</div>
                            <div class="text-muted small">Year Level: {{ $record->year_level }}</div>
                        </td>
                        <td class="px-4 py-3 small text-muted">
                            {{ date('M d, Y', strtotime($record->created_at)) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(212, 175, 55, 0.1); color: #856404; border: 1px solid #d4af37; font-size: 0.75rem;">
                                PENDING
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('admin.records.approve', $record->enrollment_id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm text-white px-3" style="background-color: #800000; border-radius: 8px; font-size: 0.8rem;">
                                        Approve
                                    </button>
                                </form>

                                <a href="{{ route('admin.records.edit', $record->enrollment_id) }}" 
                                   class="btn btn-sm btn-outline-primary px-3" 
                                   style="border-radius: 8px; font-size: 0.8rem;">
                                   Edit
                                </a>

                                <form action="{{ route('admin.records.reject', $record->enrollment_id) }}" method="POST" class="m-0" onsubmit="return confirm('Reject this enrollment?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3" style="border-radius: 8px; font-size: 0.8rem;">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No pending enrollment requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Styling consistency with Approved UI */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table td {
        border-bottom: 1px solid #f1f1f1;
    }
</style>
@endsection