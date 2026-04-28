@extends('layouts.app')

@section('content')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
    }

    .admin-main-container { 
        padding-top: 40px; 
        padding-bottom: 80px; 
    }

    .admin-page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 15px;
    }

    .content-card {
        border-radius: 20px; 
        background: #fff;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .input-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        color: #888;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .status-pill {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        font-family: 'Orbitron', sans-serif;
        text-transform: uppercase;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .btn-action-main {
        background: var(--um-maroon);
        color: white !important;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        transition: 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-action-main:hover {
        background: #600000;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(128, 0, 0, 0.2);
    }

    /* Horizontal Table Layout Fix */
    .um-custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .um-custom-table th {
        padding: 12px;
        border-bottom: 2px solid #f8f9fa;
    }

    .um-custom-table td {
        padding: 15px 12px;
        background: #fff;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }
</style>

<div class="admin-main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10"> 
                
                <h1 class="admin-page-title">Pending Enrollments</h1>
                <p class="text-center text-muted mb-5">Registrar Staff: Review and verify student enrollment requests for the current period.</p>

                <div class="card content-card p-5">
                    <div class="table-responsive">
                        <table class="um-custom-table">
                            <thead>
                                <tr>
                                    <th class="input-label" style="width: 30%;">STUDENT NAME</th>
                                    <th class="input-label" style="width: 35%;">COURSE & YEAR</th>
                                    <th class="input-label text-center" style="width: 15%;">STATUS</th>
                                    <th class="input-label text-end" style="width: 20%;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingEnrollments as $enrollment)
                                <tr>
                                    <td class="fw-bold">
                                        {{-- Now uses the getNameAttribute() from Student model --}}
                                        {{ $enrollment->student->name ?? 'Unknown Student' }}
                                    </td>
                                    
                                    <td>
                                        <div class="text-dark small fw-bold">
                                            {{ $enrollment->student->course->course_name ?? 'No Course Assigned' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            Year Level: {{ $enrollment->student->year_level ?? 'N/A' }}
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="status-pill">
                                            {{ $enrollment->status }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-end">
                                        <a href="{{ route('registrar.verify', $enrollment->enrollment_id) }}" class="btn-action-main">
                                            VERIFY &rsaquo;
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <p class="text-muted mb-0">No pending enrollment requests found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="text-muted small fw-bold text-decoration-none" style="font-family: 'Orbitron';">
                            &lsaquo; RETURN TO DASHBOARD
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection