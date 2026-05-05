@extends('layouts.app')

@section('content')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
        --surface: #ffffff;
        --subtle-gray: #f8f9fa;
    }

    .admin-main-container { padding: 40px 0 80px 0; }
    .admin-page-title { font-family: 'Orbitron', sans-serif; color: var(--um-maroon); font-weight: 700; font-size: 2.2rem; text-align: center; }

    /* Stat Cards Alignment & Spacing */
    .stat-card {
        border-radius: 15px;
        background: var(--surface);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        padding: 30px;
        height: 100%;
        transition: transform 0.3s ease;
        border-left: 5px solid transparent;
        margin-bottom: 24px; /* Fixes vertical hugging between cards */
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-border-gold { border-left-color: var(--um-gold); }
    .stat-border-maroon { border-left-color: var(--um-maroon); }

    /* The "Catalog" / Table Section */
    .content-card {
        border-radius: 20px;
        background: var(--surface);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 0; 
        overflow: hidden;
        margin-top: 15px;
    }

    /* Fixed Table Spacing */
    .custom-dashboard-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .custom-dashboard-table thead {
        background-color: #fafafa;
        border-bottom: 2px solid #eee;
    }

    .table-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        color: #999;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 22px 25px; /* Increased vertical padding */
        font-weight: 700;
    }

    .custom-dashboard-table tbody tr {
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s;
    }

    .custom-dashboard-table tbody tr:hover {
        background-color: #fffdf9;
    }

    .cell-data {
        padding: 24px 25px; /* Added more breathing room for student rows */
        vertical-align: middle;
    }

    .student-name { color: #333; font-weight: 700; font-size: 0.95rem; }
    .course-text { color: #666; font-size: 0.85rem; }
    
    /* Status Badges */
    .badge-pending {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.6rem;
        padding: 6px 14px;
        border-radius: 4px;
        background: #fff9e6;
        color: #d4a017;
        border: 1px solid #ffeeba;
        text-transform: uppercase;
    }

    /* Action Button */
    .btn-details {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--um-maroon);
        text-decoration: none;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.2s;
        display: inline-block;
    }
    .btn-details:hover {
        background: var(--um-maroon);
        color: white;
        border-color: var(--um-maroon);
    }
</style>



<div class="admin-main-container">
    <div class="container">
        
        <div class="text-center mb-5">
            <h1 class="admin-page-title">REGISTRAR <span style="color: var(--um-gold);">DASHBOARD</span></h1>
            <p class="text-muted">Verification and Enrollment Management System 2025-2026</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="stat-card stat-border-gold">
                    <span class="table-label" style="padding:0; display:block; margin-bottom:10px;">Pending Verification</span>
                    <h2 class="fw-bold my-2" style="color: var(--um-gold);">{{ $pendingCount ?? '0' }}</h2>
                    <p class="text-muted small mb-4">Requests requiring registrar staff review.</p>
                    <a href="{{ route('registrar.pending') }}" class="btn-details">REVIEW PENDING LIST ›</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card stat-border-maroon">
                    <span class="table-label" style="padding:0; display:block; margin-bottom:10px;">Approved Students</span>
                    <h2 class="fw-bold my-2" style="color: var(--um-maroon);">{{ $approvedCount ?? '0' }}</h2>
                    <p class="text-muted small mb-4">Verified and officially enrolled records.</p>
                    <a href="{{ route('admin.enrollments.approved') }}" class="btn-details">VIEW MASTER LIST ›</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h4 class="table-label mb-3" style="padding-left:10px; color: var(--um-maroon);">Recently Processed Enrollments</h4>
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="custom-dashboard-table">
                            <thead> 
                                <tr>
                                    <th class="table-label" style="width: 25%;">Student Name</th>
                                    <th class="table-label" style="width: 35%;">Course</th>
                                    <th class="table-label text-center" style="width: 15%;">Year Level</th>
                                    <th class="table-label text-center" style="width: 15%;">Status</th>
                                    <th class="table-label text-end" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnrollments as $enrollment)
                                <tr>
                                    <td class="cell-data student-name">
                                        {{ $enrollment->student->first_name ?? '' }} {{ $enrollment->student->last_name ?? 'N/A' }}
                                    </td>
                                    <td class="cell-data course-text">
                                        {{ $enrollment->student->course->course_name ?? 'N/A' }}
                                    </td>
                                    <td class="cell-data text-center small">
                                        {{ $enrollment->student->year_level ?? 'N/A' }}
                                    </td>
                                    <td class="cell-data text-center">
                                        <span class="badge-pending">{{ $enrollment->status ?? 'PENDING' }}</span>
                                    </td>
                                    <td class="cell-data text-end">
                                        <a href="{{ route('registrar.verify', $enrollment->enrollment_id) }}" class="btn-details">DETAILS</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="cell-data text-center text-muted py-5">
                                        No recent activity to display.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection