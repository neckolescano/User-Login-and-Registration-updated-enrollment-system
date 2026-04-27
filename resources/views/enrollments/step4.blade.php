@extends('layouts.app')

@push('styles')
<style>
    .enrollment-main-container {
        padding-top: 0px;
        padding-bottom: 80px;
    }

    .stepper-horizontal {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 60px;
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

    .step-unit.done .step-circle {
        border-color: var(--um-maroon);
        background-color: var(--um-maroon);
        color: #fff;
    }

    .step-unit.active .step-circle {
        border-color: var(--um-maroon);
        background-color: var(--um-maroon);
        color: #fff;
    }

    .step-label {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
        color: #666;
        text-transform: uppercase;
    }

    .page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 15px;
    }

    /* --- Review Section Styling --- */
    .integrated-review-wrapper {
        margin-top: 50px;
        border-top: 2px solid #eee;
        padding-top: 50px;
    }

    .review-info-card {
        background: rgba(0,0,0,0.02);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 40px;
        border-left: 5px solid var(--um-gold);
    }

    .review-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
    }

    .review-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
    }

    .btn-finalize {
        background: #28a745;
        color: white;
        border: none;
        padding: 18px;
        border-radius: 50px;
        width: 100%;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        text-transform: uppercase;
        margin-top: 30px;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    }
</style>
@endpush

@section('content')
<div class="enrollment-main-container">
    <div class="container"> 
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                  {{-- 3-Step Stepper --}}
               <div class="stepper-horizontal">
                    <div class="step-unit {{ Request::routeIs('enrollments.step3') ? 'active' : '' }}">
                        <div class="step-circle">01</div>
                        <div class="step-label">SUBJECTS</div>
                    </div>
                    <div class="step-unit {{ Request::routeIs('enrollments.step4') ? 'active' : '' }}">
                        <div class="step-circle">02</div>
                        <div class="step-label">REVIEW</div>
                    </div>
                    <div class="step-unit {{ Request::routeIs('enrollments.success') ? 'active' : '' }}">
                        <div class="step-circle">03</div>
                        <div class="step-label">SUCCESS</div>
                    </div>
                </div>

                <h1 class="page-title">Final Review</h1>
                <p class="text-center text-muted">Confirm your details before finishing.</p>
                
                <div class="integrated-review-wrapper">
                    
                    <div class="review-info-card">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="review-label">Semester</span>
                                <span class="review-value">{{ $period['semester'] }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="review-label">School Year</span>
                                <span class="review-value">{{ $period['school_year'] }}</span>
                            </div>
                        </div>
                    </div>

                    <h3 style="font-family: 'Orbitron'; font-size: 1.1rem; color: var(--um-maroon); margin-bottom: 20px;">
                        Summary of Sections
                    </h3>

                    <ul class="list-group list-group-flush mb-5">
                        @foreach($selectedSections as $section)
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block">{{ $section->subject->subject_code }} - {{ $section->subject->subject_name }}</strong>
                                <small class="text-muted">{{ $section->schedule }}</small>
                            </div>
                            <span class="badge" style="background: var(--um-maroon); color: #fff;">{{ $section->section_name }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <form action="{{ route('enrollments.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-finalize">
                            Confirm & Finalize Enrollment
                        </button>
                        <a href="{{ route('enrollments.step3') }}" class="btn btn-link mt-3 text-muted d-block text-center">Change Subjects</a>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection