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
            <div class="col-lg-8"> <h1 class="admin-page-title">Add New Course</h1>
                <p class="text-center text-muted mb-5">Admin Tool: Define a new degree program for the university database.</p>

                <div class="card border-0 shadow-sm p-5" style="border-radius: 20px; background: #fff;">
                    <form action="{{ route('admin.courses.store') }}" method="POST">
                        @csrf
                        
                        {{-- Course Name - Full Width --}}
                        <div class="row">
                            <div class="col-12">
                                <label class="input-label">COURSE NAME</label>
                                <input type="text" name="course_name" class="integrated-input" placeholder="e.g., Bachelor of Science in Information Technology" required>
                            </div>
                        </div>

                        {{-- Department Selection - Full Width --}}
                        <div class="row">
                            <div class="col-12">
                                <label class="input-label">ASSIGN TO DEPARTMENT</label>
                                <select name="department_id" class="integrated-input" required>
                                    <option value="" disabled selected>Select a Department...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Action Button Group matching Student Layout --}}
                        <div class="btn-action-group">
                            <a href="{{ route('dashboard') }}" class="text-muted small fw-bold text-decoration-none" style="font-family: 'Orbitron';">
                                &lsaquo; CANCEL AND RETURN
                            </a>
                            <button type="submit" class="btn-save">
                                CREATE COURSE &rsaquo;
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection