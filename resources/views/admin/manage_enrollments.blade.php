@extends('layouts.app')

@push('styles')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
        --bg-light: #f4f7f6;
    }

    /* Main container consistency */
    .admin-main-container { 
        padding-top: 40px; 
        padding-bottom: 80px; 
    }

    .admin-page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 10px;
        letter-spacing: 2px;
    }

    /* Table styling to match the clean integrated look */
    .admin-card {
        background: white;
        border-radius: 30px;
        padding: 40px;
        margin-top: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .admin-table-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        color: #bbb;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #f8f8f8;
        padding-bottom: 15px;
        font-weight: 700;
    }

    .student-name-text {
        font-weight: 700;
        color: #333;
        font-size: 1rem;
    }

    .course-info-box {
        font-size: 0.85rem;
        color: #777;
    }

    .status-badge-pending {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        padding: 6px 15px;
        border-radius: 50px;
        background: #fffcf0;
        color: var(--um-gold);
        border: 1px solid var(--um-gold);
        text-transform: uppercase;
        font-weight: 700;
    }

    /* Vertical Action Stack */
    .action-cell-stack {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .btn-approve-sm {
        background: var(--um-maroon);
        color: white;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 50px;
        padding: 8px 25px;
        text-transform: uppercase;
        border: none;
        transition: 0.3s;
        width: 120px;
        box-shadow: 0 4px 10px rgba(128, 0, 0, 0.1);
    }

    .btn-approve-sm:hover {
        background: #600000;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(128, 0, 0, 0.2);
    }

    .btn-details-link {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        color: #aaa;
        text-decoration: none;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-details-link:hover {
        color: #333;
    }

    .btn-reject-link {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        color: #e74c3c;
        background: none;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-reject-link:hover {
        opacity: 0.7;
    }

    .action-link-main {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.85rem;
        transition: 0.3s;
    }

    .action-link-main:hover {
        color: var(--um-gold);
    }
</style>
@endpush

@section('content')
<div class="admin-main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <h1 class="admin-page-title">Manage Enrollments</h1>
                <p class="text-center text-muted mb-4">Review and verify student enrollment requests for the current period.</p>

                <div class="text-center mb-5">
                    <a href="{{ route('admin.enrollments.approved') }}" class="action-link-main">
                        VIEW APPROVED LIST &rsaquo;
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert-success mb-4" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 12px; border: 1px solid #c3e6cb; text-align: center;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="admin-card">
                    <div class="table-responsive">
                        <table class="custom-table table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="admin-table-label text-center" style="width: 25%;">Student Name</th>
                                    <th class="admin-table-label text-center" style="width: 30%;">Course & Year</th>
                                    <th class="admin-table-label text-center" style="width: 20%;">Date Submitted</th>
                                    <th class="admin-table-label text-center" style="width: 10%;">Status</th>
                                    <th class="admin-table-label text-center" style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $record)
                                <tr>
                                    <td class="text-center py-4">
                                        <div class="student-name-text">{{ $record->student->first_name }} {{ $record->student->last_name }}</div>
                                    </td>
                                    <td class="text-center py-4">
                                        <div class="course-info-box">
                                            <strong style="color: #444;">{{ $record->student->course->course_name ?? 'N/A' }}</strong><br>
                                            Year Level: {{ $record->student->year_level }}
                                        </div>
                                    </td>
                                    <td class="text-center py-4 text-muted small">
                                        {{ $record->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-center py-4">
                                        <span class="status-badge-pending">pending</span>
                                    </td>
                                    <td class="text-center py-4">
                                        <div class="action-cell-stack">
                                            <form action="{{ route('admin.records.approve', $record->enrollment_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-approve-sm">Approve</button>
                                            </form>

                                            <a href="{{ route('admin.records.edit', $record->enrollment_id) }}" class="btn-details-link">DETAILS</a>

                                            <form action="{{ route('admin.records.reject', $record->enrollment_id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-reject-link">REJECT</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted" style="font-family: 'Orbitron';">No pending requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('dashboard') }}" class="text-muted small" style="text-decoration: none; font-family: 'Orbitron'; letter-spacing: 1px;">
                        &lsaquo; RETURN TO DASHBOARD
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection