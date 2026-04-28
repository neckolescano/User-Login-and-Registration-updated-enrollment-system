    <?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\EnrollmentController; 
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\CourseController;
    use App\Http\Controllers\DepartmentController;
    use App\Http\Controllers\InstructorController;
    use App\Http\Controllers\SectionController;
    use App\Http\Controllers\RegistrarController; 
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | 1. GUEST ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/', function () {
        return view('home');
    })->middleware('guest');

    /*
    |--------------------------------------------------------------------------
    | 2. AUTHENTICATED ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        
        // The "Traffic Controller" - Sends users to the right dashboard based on role
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Redirect root to the dashboard logic for logged-in users
        Route::get('/home', [DashboardController::class, 'index'])->name('home');

        /* STUDENT ONLY - Refactored to 3-Step UI */
        Route::middleware('can:student-only')->group(function () {
            Route::get('/enroll', [EnrollmentController::class, 'showStep3'])->name('enrollments.step3');
            Route::post('/enroll/subjects', [EnrollmentController::class, 'postStep3'])->name('enrollments.post.step3');
            
            Route::get('/enroll/review', [EnrollmentController::class, 'showStep4'])->name('enrollments.step4');
            Route::post('/enroll/store', [EnrollmentController::class, 'store'])->name('enrollments.store');
            Route::get('/enroll/success', [EnrollmentController::class, 'success'])->name('enrollments.success');
            
            Route::get('/my-enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
            Route::get('/my-enrollments/{id}', [EnrollmentController::class, 'show'])->name('enrollments.show');
        });

        /* REGISTRAR & ADMIN SHARED (The Verification Desk) */
        Route::middleware('can:registrar-access')->group(function () {
            
            // --- Registrar Specific Portal ---
            Route::prefix('registrar')->group(function () {
                // This name must be unique to avoid overlapping with the main dashboard
                Route::get('/portal', [RegistrarController::class, 'index'])->name('registrar.dashboard');
                Route::get('/pending', [RegistrarController::class, 'pending'])->name('registrar.pending');
                Route::get('/verify/{id}', [RegistrarController::class, 'verify'])->name('registrar.verify');
                Route::post('/approve/{id}', [RegistrarController::class, 'approve'])->name('registrar.approve');
            });

            // Records Management
            Route::get('/records', [AdminController::class, 'allRecords'])->name('admin.manage_enrollments');
            Route::get('/records/list', [AdminController::class, 'allRecords'])->name('admin.records.index');
            Route::get('/records/approved', [AdminController::class, 'approvedRecords'])->name('admin.enrollments.approved');
            Route::delete('/records/{id}', [AdminController::class, 'destroy'])->name('admin.records.destroy');

            Route::post('/records/{id}/approve', [AdminController::class, 'approve'])->name('admin.records.approve');
            Route::post('/records/{id}/reject', [AdminController::class, 'reject'])->name('admin.records.reject'); 
            
            Route::get('/records/{id}/edit', [AdminController::class, 'edit'])->name('admin.records.edit'); 
            Route::patch('/records/{id}/update', [AdminController::class, 'updateRecord'])->name('admin.records.update');

            // Resource Controllers (Available to Admin/Registrar)
            Route::resource('courses', CourseController::class)->names(['create' => 'admin.courses.create','store' => 'admin.courses.store']);
            Route::resource('departments', DepartmentController::class)->names(['create' => 'admin.departments.create','store' => 'admin.departments.store']);
            Route::resource('instructors', InstructorController::class)->names(['create' => 'admin.instructors.create','store' => 'admin.instructors.store']);
            Route::resource('sections', SectionController::class)->names(['create' => 'admin.sections.create','store' => 'admin.sections.store']);
        });

        /* ADMINISTRATOR ONLY (System Management) */
        Route::middleware('can:admin-only')->group(function () {
            Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users.index');
            Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
            Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');

            Route::get('/admin/subjects/add', [AdminController::class, 'create'])->name('admin.add_subject');
            Route::post('/admin/subjects/store', [AdminController::class, 'storeSubject'])->name('admin.subjects.store');

            Route::resource('admin/departments', AdminController::class)->except(['create', 'store']);
            Route::resource('admin/courses', AdminController::class)->except(['create', 'store']);
            Route::resource('admin/instructors', AdminController::class);
            Route::resource('admin/sections', AdminController::class);
        });

        // Profile Management
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';