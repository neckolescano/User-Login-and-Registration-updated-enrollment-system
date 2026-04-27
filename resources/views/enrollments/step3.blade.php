@extends('layouts.app')

@push('styles')
<style>
    /* --- Main Page Container --- */
    .enrollment-main-container {
        padding-top: 30px;
        padding-bottom: 50px;
        position: relative;
    }

    .stepper-horizontal {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 50px;
        position: relative;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .stepper-horizontal::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 10%;
        right: 10%;
        height: 3px;
        background-color: #e0e0e0;
        z-index: 1;
    }

    .step-unit {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 2; 
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px auto;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 1.2rem;
        color: #888;
    }

    .step-unit.active .step-circle {
        border-color: var(--um-maroon);
        background-color: var(--um-maroon);
        color: #fff;
        box-shadow: 0 0 0 5px rgba(128, 0, 0, 0.15);
    }

    .step-label {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: #666;
        text-transform: uppercase;
    }

    .active .step-label {
        color: var(--um-maroon);
    }

    .page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 10px;
    }

    /* --- Table Styling --- */
    .integrated-table-wrapper {
        margin-top: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #eee;
    }

    .subject-table {
        width: 100%;
        border-collapse: collapse;
    }

    .subject-table th {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        border-bottom: 3px solid var(--um-gold);
        padding: 18px 15px;
        text-transform: uppercase;
        font-size: 0.85rem;
        background: #fcfcfc;
    }

    .subject-row td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: 0.2s;
    }

    .subject-row:hover td {
        background-color: rgba(128, 0, 0, 0.02);
    }

    .custom-checkbox {
        width: 22px;
        height: 22px;
        accent-color: var(--um-maroon);
        cursor: pointer;
    }

    /* --- THE FIX: Integrated Summary Bar --- */
    .unit-summary-card {
        margin-top: 30px;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        border-top: 4px solid var(--um-gold);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        /* Standard flow - no more fixed/covering */
        position: relative; 
    }

    .unit-display {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 1.4rem;
        color: #333;
    }

    .unit-limit-warning {
        color: #dc3545;
        font-size: 0.85rem;
        display: none;
        font-weight: bold;
        margin-top: 5px;
    }

    .btn-proceed {
        background: var(--um-maroon);
        color: white;
        border: none;
        padding: 15px 45px;
        border-radius: 50px;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.3s ease;
        box-shadow: 0 5px 15px rgba(128, 0, 0, 0.2);
    }

    .btn-proceed:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(128, 0, 0, 0.3);
    }

    .btn-proceed:disabled {
        background: #ccc;
        cursor: not-allowed;
        box-shadow: none;
    }
</style>
@endpush

@section('content')
<div class="enrollment-main-container">
    <div class="container"> 
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                {{-- 3-Step Stepper --}}
                <div class="stepper-horizontal">
                    <div class="step-unit active">
                        <div class="step-circle">01</div>
                        <div class="step-label">SUBJECTS</div>
                    </div>
                    <div class="step-unit">
                        <div class="step-circle">02</div>
                        <div class="step-label">REVIEW</div>
                    </div>
                    <div class="step-unit">
                        <div class="step-circle">03</div>
                        <div class="step-label">SUCCESS</div>
                    </div>
                </div>

                <h1 class="page-title">Select Subjects</h1>
                <p class="text-center text-muted">Choose the sections you wish to enroll in for this period.</p>
                
                @if(session('error'))
                    <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid darkred !important;">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('enrollments.post.step3') }}" method="POST" id="enrollmentForm">
                    @csrf
                    
                    <div class="integrated-table-wrapper">
                        <table class="subject-table">
                            <thead>
                                <tr>
                                    <th width="80" class="text-center">SELECT</th>
                                    <th>CODE</th>
                                    <th>SUBJECT NAME</th>
                                    <th class="text-center">UNITS</th>
                                    <th>SECTION</th>
                                    <th>SCHEDULE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sections as $section)
                                <tr class="subject-row">
                                    <td class="text-center">
                                        <input type="checkbox" name="section_ids[]" value="{{ $section->section_id }}" 
                                               class="custom-checkbox subject-checkbox" 
                                               data-units="{{ $section->subject->units }}">
                                    </td>
                                    <td><strong>{{ $section->subject->subject_code }}</strong></td>
                                    <td>{{ $section->subject->subject_name }}</td>
                                    <td class="fw-bold text-center">{{ $section->subject->units }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $section->section_name }}</span></td>
                                    <td class="text-muted small">{{ $section->schedule }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">No subjects found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Integrated Summary Section (No longer overlapping) --}}
                    <div class="unit-summary-card">
                        <div class="row align-items-center">
                            <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                                <div class="unit-display">
                                    TOTAL SELECTED: <span id="unit-count">0</span> / 26 UNITS
                                </div>
                                <div id="unit-warning" class="unit-limit-warning">
                                    <i class="fas fa-exclamation-circle me-1"></i> MAXIMUM UNIT LIMIT EXCEEDED!
                                </div>
                            </div>
                            <div class="col-md-5 text-center text-md-end">
                                <button type="submit" id="submitBtn" class="btn-proceed" disabled>
                                    Review Selection &rsaquo;
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.subject-checkbox');
    const unitCountDisplay = document.getElementById('unit-count');
    const submitBtn = document.getElementById('submitBtn');
    const warning = document.getElementById('unit-warning');

    function calculateUnits() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseInt(cb.getAttribute('data-units'));
            }
        });

        unitCountDisplay.innerText = total;

        if (total > 26) {
            unitCountDisplay.style.color = '#dc3545';
            warning.style.display = 'block';
            submitBtn.disabled = true;
        } else if (total > 0) {
            unitCountDisplay.style.color = 'var(--um-maroon)';
            warning.style.display = 'none';
            submitBtn.disabled = false;
        } else {
            unitCountDisplay.style.color = '#333';
            warning.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', calculateUnits);
    });
});
</script>
@endsection