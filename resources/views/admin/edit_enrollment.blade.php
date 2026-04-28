<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Enrollment | UM</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --um-maroon: #800000; --um-gold: #d4af37; --bg-light: #f4f7f6; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); margin: 0; padding: 40px 20px; color: #333; }
        .enrollment-card { max-width: 1000px; margin: 0 auto; background: #fff; border-radius: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 20px; }
        .card-header { padding: 40px; border-bottom: 2px solid #eee; }
        .header-title { font-family: 'Orbitron', sans-serif; color: var(--um-maroon); font-size: 2rem; margin: 0; text-transform: uppercase; }
        .header-title span { color: var(--um-gold); }
        .student-info { font-size: 1rem; color: #888; margin-top: 10px; }
        .form-body { padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 50px; }
        label { display: block; font-family: 'Orbitron', sans-serif; font-size: 0.75rem; font-weight: 700; color: #aaa; margin-bottom: 12px; }
        select { width: 100%; padding: 15px; border: 2px solid #eee; border-radius: 12px; background: #fafafa; font-family: 'Inter', sans-serif; }
        .table-title { font-family: 'Orbitron', sans-serif; font-size: 0.85rem; color: var(--um-maroon); margin-bottom: 20px; border-left: 4px solid var(--um-gold); padding-left: 15px; }
        .subject-table { width: 100%; border-collapse: collapse; }
        .subject-table th { text-align: left; padding: 15px; font-family: 'Orbitron', sans-serif; font-size: 0.7rem; color: #aaa; border-bottom: 2px solid #f5f5f5; }
        .subject-table td { padding: 20px 15px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }
        .badge-schedule { background: #f8f9fa; padding: 8px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; border: 1px solid #eee; }
        .actions { display: flex; justify-content: flex-end; align-items: center; gap: 25px; margin-top: 50px; padding-top: 30px; border-top: 2px solid #eee; }
        .btn-cancel { text-decoration: none; color: #888; font-family: 'Orbitron', sans-serif; font-size: 0.75rem; font-weight: 700; }
        .btn-commit { background: var(--um-maroon); color: #fff; border: none; padding: 15px 40px; border-radius: 50px; font-family: 'Orbitron', sans-serif; font-size: 0.8rem; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

<div class="enrollment-card">
    <div class="card-header">
        <h1 class="header-title">EDIT <span>ENROLLMENT</span></h1>
        <div class="student-info">
            Modifying record for: <strong>{{ $record->student->first_name }} {{ $record->student->last_name }}</strong> 
            <span style="margin: 0 15px; color: #eee;">|</span> 
            <span>ID: #{{ $record->enrollment_id }}</span>
        </div>
    </div>

    <div class="form-body">
        <form action="{{ route('admin.records.update', $record->enrollment_id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <div>
                    <label>Year Level</label>
                    <select name="year_level">
                        @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year'] as $level)
                            <option value="{{ $level }}" {{ $record->student->year_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Semester</label>
                    <select name="semester">
                        <option value="1st Semester" {{ $record->semester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ $record->semester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    </select>
                </div>
            </div>

            <div class="table-title">ENROLLED SUBJECT SCHEDULE</div>
            <table class="subject-table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Course Title</th>
                        <th>Assigned Schedule</th>
                        <th>Available Sections</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrolledSubjects as $detail)
                    <tr>
                        <td><strong style="color: var(--um-maroon);">{{ $detail->section->subject->subject_code }}</strong></td>
                        <td style="color: #444; font-weight: 600;">{{ $detail->section->subject->subject_name }}</td>
                        <td><span class="badge-schedule">{{ $detail->section->schedule }}</span></td>
                        <td>
                            <select name="sections[{{ $detail->detail_id }}]" style="padding: 8px; font-size: 0.85rem; border-color: #eee;">
                                @foreach($allSections->where('subject_id', $detail->section->subject_id) as $section)
                                    @php $isFull = ($section->remaining_slots <= 0 && $detail->section_id != $section->section_id); @endphp
                                    <option value="{{ $section->section_id }}" {{ $detail->section_id == $section->section_id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                        {{ $section->schedule }} (Slots: {{ $section->remaining_slots }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="actions">
                <a href="{{ route('admin.manage_enrollments') }}" class="btn-cancel">CANCEL</a>
                <button type="submit" class="btn-commit">COMMIT CHANGES</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>