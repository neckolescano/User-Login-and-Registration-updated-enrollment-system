@extends('layouts.app')

@section('content')
<style>
    :root {
        --um-maroon: #800000;
        --um-gold: #d4af37;
    }

    /* Main container matching the reference layout */
    .admin-main-container { 
        padding-top: 40px; 
        padding-bottom: 80px; 
    }

    /* Standard Page Title matching Register Student */
    .admin-page-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--um-maroon);
        font-weight: 700;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 15px;
    }

    /* Integrated Input & Select Styling */
    .integrated-input {
        width: 100%;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #eee;
        background-color: #fcfcfc; 
        transition: 0.3s;
        margin-bottom: 20px;
    }

    .integrated-input:focus {
        border-color: var(--um-gold);
        outline: none;
        background-color: #fff;
    }

    /* Tech-style Labels */
    .input-label {
        font-family: 'Orbitron', sans-serif;
        font-size: 0.75rem;
        color: #888;
        margin-bottom: 8px;
        display: block;
        letter-spacing: 1px;
        font-weight: 700;
    }

    /* Action Buttons */
    .btn-action-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
    }

    .btn-save {
        background: var(--um-maroon);
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 12px;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        transition: 0.3s ease;
    }

    .btn-save:hover {
        background: #600000;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(128, 0, 0, 0.2);
    }
</style>

<div class="admin-main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8"> 
                
                <h1 class="admin-page-title">Create Section</h1>
                <p class="text-center text-muted mb-5">Admin Tool: Configure subject schedules and faculty assignments.</p>

                <div class="card border-0 shadow-sm p-5" style="border-radius: 20px; background: #fff;">
                    <form action="{{ route('admin.sections.store') }}" method="POST">
                        @csrf
                        
                        {{-- Row 1: Section Name & Academic Term --}}
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="input-label">SECTION NAME</label>
                                <input type="text" name="section_name" class="integrated-input" placeholder="e.g., S-IT101" required>
                            </div>
                            <div class="col-md-4">
                                <label class="input-label">SEMESTER</label>
                                <select name="semester" class="integrated-input" required>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="input-label">SCHOOL YEAR</label>
                                <input type="text" name="school_year" class="integrated-input" value="2025-2026" required>
                            </div>
                        </div>

                        {{-- Row 2: Subject & Dynamic Instructor --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="input-label">SUBJECT</label>
                                <select name="subject_id" id="subject_select" class="integrated-input" required>
                                    <option value="" disabled selected>Select Subject...</option>
                                    @foreach($subjects as $sub)
                                        {{-- Using the department_id from our Controller Join --}}
                                        <option value="{{ $sub->subject_id }}" data-dept="{{ $sub->department_id }}">
                                            {{ $sub->subject_code }} - {{ $sub->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="input-label">ASSIGN INSTRUCTOR</label>
                                <select name="instructor_id" id="instructor_select" class="integrated-input" disabled required>
                                    <option value="" disabled selected>Select a Subject First...</option>
                                </select>
                            </div>
                        </div>

                        {{-- Row 3: Schedule --}}
                        <div class="row">
                            <div class="col-12">
                                <label class="input-label">SCHEDULE (DAYS & TIME)</label>
                                <input type="text" name="schedule" class="integrated-input" placeholder="e.g., MW 8:00AM - 10:30AM" required>
                            </div>
                        </div>

                        {{-- Row 4: Room & Capacity --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="input-label">ROOM / LABORATORY</label>
                                <input type="text" name="room" class="integrated-input" placeholder="e.g., Room 402" required>
                            </div>
                            <div class="col-md-6">
                                <label class="input-label">MAX CAPACITY</label>
                                <input type="number" name="capacity" class="integrated-input" value="40" required>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="btn-action-group">
                            <a href="{{ route('dashboard') }}" class="text-muted small fw-bold text-decoration-none" style="font-family: 'Orbitron';">
                                &lsaquo; CANCEL AND RETURN
                            </a>
                            <button type="submit" class="btn-save">
                                CREATE SECTION &rsaquo;
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Dynamic Instructor Filtering Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Pass the instructors as a JSON object for front-end filtering
    const allInstructors = @json($instructors); 

    $('#subject_select').change(function() {
        const selectedDept = $(this).find(':selected').data('dept');
        const $instSelect = $('#instructor_select');
        
        // Reset the instructor dropdown
        $instSelect.empty().append('<option value="" disabled selected>Select Instructor...</option>');
        
        if (selectedDept) {
            // Filter instructors who match the subject's department_id
            const filtered = allInstructors.filter(i => i.department_id == selectedDept);
            
            if (filtered.length > 0) {
                $instSelect.prop('disabled', false);
                filtered.forEach(i => {
                    // Logic to handle full_name or first/last name
                    const name = i.instructor_name ? i.instructor_name : (i.first_name + ' ' + i.last_name);
                    $instSelect.append(`<option value="${i.instructor_id}">${name}</option>`);
                });
            } else {
                $instSelect.prop('disabled', true).append('<option value="" disabled>No instructors available for this dept</option>');
            }
        } else {
            $instSelect.prop('disabled', true);
        }
    });
});
</script>
@endsection