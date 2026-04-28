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

    .status-badge-approved {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        padding: 6px 15px;
        border-radius: 50px;
        background: #f0fff4;
        color: #2ecc71;
        border: 1px solid #2ecc71;
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

    .btn-edit-sm {
        background: #f8f9fa;
        color: #666;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 50px;
        padding: 8px 25px;
        text-transform: uppercase;
        border: 1px solid #eee;
        transition: 0.3s;
        width: 130px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-edit-sm:hover {
        background: #eee;
        color: #333;
        transform: translateY(-2px);
    }

    .btn-revoke-link {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        color: #e74c3c;
        background: none;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
    }

    .btn-revoke-link:hover {
        opacity: 0.7;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="admin-main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <h1 class="admin-page-title">Master Enrollment List</h1>
                <p class="text-center text-muted mb-5">Official database of confirmed academic records for the current period.</p>

                <div class="admin-card">
                    <div class="table-responsive">
                        <table class="custom-table table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="admin-table-label text-center" style="width: 25%;">Student Name</th>
                                    <th class="admin-table-label text-center" style="width: 25%;">Course & Year</th>
                                    <th class="admin-table-label text-center" style="width: 20%;">SY / Semester</th>
                                    <th class="admin-table-label text-center" style="width: 15%;">Status</th>
                                    <th class="admin-table-label text-center" style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                <tr>
                                    <td class="text-center py-4">
                                        <div class="student-name-text">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</div>
                                    </td>
                                    <td class="text-center py-4">
                                        <div class="course-info-box">
                                            <strong style="color: #444;">{{ $enrollment->student->course->course_name ?? 'N/A' }}</strong><br>
                                            Year Level: {{ $enrollment->student->year_level }}
                                        </div>
                                    </td>
                                    <td class="text-center py-4 text-muted small">
                                        <span class="fw-600">2025-2026</span><br>
                                        <span class="fw-bold" style="color: var(--um-maroon); font-family: 'Orbitron';">{{ $enrollment->semester }}</span>
                                    </td>
                                    <td class="text-center py-4">
                                        <span class="status-badge-approved">approved</span>
                                    </td>
                                    <td class="text-center py-4">
                                        <div class="action-cell-stack">
                                            <a href="{{ route('admin.records.edit', $enrollment->enrollment_id) }}" class="btn-edit-sm">Edit Record</a>
                                            
                                            <form action="{{ route('admin.records.destroy', $enrollment->enrollment_id) }}" method="POST" onsubmit="return confirm('Revoke enrollment? This will remove the student from the master list.')">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn-revoke-link">Revoke</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted" style="font-family: 'Orbitron';">No approved records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('admin.manage_enrollments') }}" class="text-muted small" style="text-decoration: none; font-family: 'Orbitron'; letter-spacing: 1px;">
                        &lsaquo; RETURN TO PENDING REQUESTS
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection