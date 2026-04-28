@extends('layouts.app')

@section('content')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
    }

    /* Standard Main Container */
    .admin-main-container { 
        padding-top: 40px; 
        padding-bottom: 80px; 
    }

    /* Page Title */
    .admin-page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 15px;
    }

    /* Standard Card Look */
    .content-card {
        border-radius: 20px;
        background: #fff;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 40px;
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

    /* Data Display Boxes */
    .data-item {
        background: #fcfcfc;
        border: 1px solid #eee;
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .data-value {
        font-size: 1.1rem;
        color: #333;
        font-weight: 600;
        display: block;
    }

    /* COR Table Styling */
    .cor-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 10px;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }

    .cor-table th {
        background: #f8f9fa;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.65rem;
        padding: 15px;
        color: #666;
        border-bottom: 2px solid #eee;
    }

    .cor-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
        color: #444;
    }

    /* Buttons */
    .btn-approve {
        background: #198754;
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 12px;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        transition: 0.3s;
        width: 100%;
    }

    .btn-approve:hover {
        background: #146c43;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
    }

    .btn-back {
        background: transparent;
        color: #888;
        border: none;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 0.75rem;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-back:hover {
        color: var(--um-maroon);
    }
</style>

<div class="admin-main-container">
    <div class="container">
        
        <h1 class="admin-page-title">VERIFY <span style="color: var(--um-gold);">ENROLLMENT</span></h1>
        <p class="text-center text-muted mb-5">Registrar Audit: Please review the Certificate of Registration (COR) before final approval.</p>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
                    {{-- Row 1: Student Header Info --}}
                    <div class="row mb-4">
                        <div class="col-md-7">
                            <div class="data-item">
                                <span class="input-label">Student Full Name</span>
                                <span class="data-value">{{ $enrollment->student->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="data-item">
                                <span class="input-label">Course & Year Level</span>
                                <span class="data-value">{{ $enrollment->student->course->course_name }} - Yr {{ $enrollment->student->year_level }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- COR Table: The Subjects the student enrolled in --}}
                    <div class="mb-5">
                        <h5 class="input-label mb-3" style="color: var(--um-maroon); font-size: 0.9rem;">Enrolled Subjects (COR)</h5>
                        <div class="table-responsive">
                            <table class="cor-table">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">CODE</th>
                                        <th style="width: 45%;">SUBJECT DESCRIPTION</th>
                                        <th style="width: 20%;" class="text-center">SECTION</th>
                                        <th style="width: 20%;" class="text-center">UNITS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollment->details as $detail)
                                    <tr>
                                        <td class="fw-bold text-maroon">{{ $detail->section->subject->subject_code }}</td>
                                        <td>{{ $detail->section->subject->subject_description }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $detail->section->section_name }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($detail->section->subject->units ?? 3.0, 1) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">No subjects found for this enrollment record.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Approval Action --}}
                    <div class="p-4 rounded-4 text-center" style="background: #f8f9fa; border: 1px dashed #ddd;">
                        <p class="small text-muted mb-4">
                            <i class="bi bi-shield-check me-1"></i> 
                            Confirming this enrollment will officially register the student and update the class lists for the sections shown above.
                        </p>

                        <form action="{{ route('registrar.approve', $enrollment->enrollment_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-approve">
                                FINAL CONFIRM & APPROVE ENROLLMENT &rsaquo;
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('registrar.pending') }}" class="btn-back">
                            &lsaquo; CANCEL AND RETURN TO LIST
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection