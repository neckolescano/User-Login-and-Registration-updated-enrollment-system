@extends('layouts.app')

@section('content')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
    }

    /* Main container matching the admin layout */
    .admin-main-container { 
        padding-top: 40px; 
        padding-bottom: 80px; 
    }

    /* Standard Page Title */
    .admin-page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 10px;
    }

    /* Stat Card Styling */
    .stat-card {
        border-radius: 15px;
        border: none;
        transition: 0.3s ease;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 24px;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    /* Colored accents for the left border */
    .stat-border-gold {
        border-left: 5px solid var(--um-gold);
    }

    .stat-border-maroon {
        border-left: 5px solid var(--um-maroon);
    }

    /* Tech-style Labels */
    .input-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        color: #888;
        margin-bottom: 8px;
        display: block;
        letter-spacing: 1px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Table styling to match the card look */
    .content-card {
        border-radius: 20px;
        background: #fff;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 30px;
    }

    /* Action Link */
    .action-link {
        color: var(--um-maroon);
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 0.8rem;
        text-decoration: none;
        transition: 0.2s;
    }

    .action-link:hover {
        color: var(--um-gold);
        text-decoration: underline;
    }
</style>

<div class="admin-main-container">
    <div class="container">
        
        {{-- Page Header --}}
        <div class="text-center mb-5">
            <h1 class="admin-page-title">REGISTRAR <span style="color: var(--um-gold);">DASHBOARD</span></h1>
            <p class="text-muted">Verification and Enrollment Management System 2025-2026</p>
        </div>

        {{-- Statistics Row --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="stat-card stat-border-gold">
                    <span class="input-label">Pending Verification</span>
                    <h2 class="fw-bold my-2" style="color: var(--um-gold);">{{ $pendingCount ?? '0' }}</h2>
                    <p class="text-muted small mb-3">Requests requiring registrar staff review.</p>
                    <a href="{{ route('registrar.pending') }}" class="action-link">
                        REVIEW PENDING LIST &rsaquo;
                    </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card stat-border-maroon">
                    <span class="input-label">Approved Students</span>
                    <h2 class="fw-bold my-2" style="color: var(--um-maroon);">{{ $approvedCount ?? '0' }}</h2>
                    <p class="text-muted small mb-3">Verified and officially enrolled records.</p>
                    <a href="{{ route('admin.enrollments.approved') }}" class="action-link">
                        VIEW MASTER LIST &rsaquo;
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Activity Table --}}
        <div class="row">
            <div class="col-12">
                <h4 class="input-label mb-3" style="font-size: 0.9rem;">Recently Processed Enrollments</h4>
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="input-label border-0">Student Name</th>
                                    <th class="input-label border-0">Course</th>
                                    <th class="input-label border-0">Year Level</th>
                                    <th class="input-label border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnrollments as $enrollment)
                                <tr>
                                    <td class="fw-bold py-3">{{ $enrollment->student_name }}</td>
                                    <td class="small text-muted">{{ $enrollment->course_name }}</td>
                                    <td class="small">{{ $enrollment->year_level }}</td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-outline-dark fw-bold" style="font-size: 0.65rem; border-radius: 8px;">
                                            DETAILS
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
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