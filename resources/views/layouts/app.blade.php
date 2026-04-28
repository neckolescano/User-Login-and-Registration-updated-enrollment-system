    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>UM Enrollment System</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Monoton&family=Orbitron:wght@400;700&family=Electrolize&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- This allows specific pages to "push" their own CSS here --}}
        @stack('styles')

        <style>
            /* 1. BRAND COLORS & RESET */
            :root {
                --um-maroon: #800000;
                --um-gold: #d4af37;
                --um-bg: #f4f4f4;
            }

            body { 
                margin: 0; padding: 0; 
                background-color: var(--um-bg) !important; 
                font-family: 'Outfit', sans-serif;
                color: #333;
            }

            /* 2. NAVBAR STYLES */
            .navbar { 
                background: #ffffff !important; 
                padding: 0.8rem 5%; 
                display: flex; 
                align-items: center;
                position: sticky; 
                top: 0;
                z-index: 1000;
                box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            }
            
            .nav-link { 
                color: #444 !important; 
                text-decoration: none; 
                padding: 0.5rem 1.2rem; 
                font-weight: 600;
                transition: 0.3s;
                font-size: 0.9rem;
                text-transform: uppercase;
            }

            .nav-link:hover {
                color: var(--um-maroon) !important;
            }

            .active { 
                color: var(--um-maroon) !important;
                border-bottom: 2px solid var(--um-gold); 
            }

            /* 3. MAIN CONTAINER */
            .container { 
                padding: 40px 3%; 
                max-width: 1200px; 
                margin: 0 auto;
                min-height: 80vh;
            }

            /* 4. BUTTONS */
            .logout-btn {
                background: var(--um-maroon);
                color: white !important;
                border: none;
                padding: 8px 20px;
                border-radius: 50px;
                cursor: pointer;
                font-family: 'Orbitron', sans-serif;
                font-size: 0.8rem;
                font-weight: bold;
                transition: 0.3s;
            }
            .logout-btn:hover { 
                background: #600000; 
                transform: scale(1.05);
            }

            /* Table Consistency */
            .um-table-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                padding: 30px;
                margin-bottom: 30px;
            }

            .um-table {
                width: 100%;
                border-collapse: collapse;
            }

            .um-table th {
                font-family: 'Orbitron', sans-serif;
                color: var(--um-maroon);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                padding: 15px 20px;
                border-bottom: 2px solid #f0f0f0;
                text-align: left;
            }

            .um-table td {
                padding: 18px 20px;
                font-size: 0.9rem;
                color: #444;
                border-bottom: 1px solid #f8f8f8;
            }

            /* Badge Styling */
            .status-badge {
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                display: inline-block;
            }
            .status-pending { background: #fff3cd; color: #856404; }
            .status-approved { background: #d4edda; color: #155724; }

            /* Action Buttons */
            .btn-um-load {
                background: var(--um-maroon);
                color: white !important;
                font-family: 'Orbitron', sans-serif;
                font-size: 0.7rem;
                padding: 8px 16px;
                border-radius: 6px;
                text-decoration: none;
                transition: 0.3s;
            }
            .btn-um-load:hover { background: #600000; transform: translateY(-2px); }

            /* 5. DROPDOWN STYLES */
            .dropdown { position: relative; display: inline-block; }
            
            .dropdown-menu {
                display: none;
                position: absolute;
                background-color: white;
                min-width: 220px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                border-radius: 12px;
                padding: 10px 0;
                top: 100%;
                left: 0;
                z-index: 1000;
                border: 1px solid #eee;
            }

            .dropdown:hover .dropdown-menu { display: block; }

            .dropdown-item {
                color: #444;
                padding: 10px 20px;
                text-decoration: none;
                display: block;
                font-size: 0.85rem;
                font-weight: 600;
                transition: 0.2s;
            }

            .dropdown-item:hover {
                background-color: #f8f8f8;
                color: var(--um-maroon);
            }

            .dropdown-divider {
                height: 1px;
                background-color: #eee;
                margin: 8px 0;
            }
        </style>
    </head>
   <body>
        <nav class="navbar">
        <div style="font-family: 'Orbitron'; font-weight: 700; font-size: 1.3rem; margin-right: 40px; color: var(--um-maroon);">
            UM <span style="color: var(--um-gold);">ENROLLMENT</span>
        </div>

        <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') || Request::is('/') ? 'active' : '' }}">
            {{ Auth::check() && Auth::user()->role === 'Administrator' ? 'ADMIN PANEL' : 'DASHBOARD' }}
        </a>
        
        @auth
           {{-- 2. REGISTRAR ONLY --}}
            @if(Auth::user()->role === 'Registrar Staff')
                <a href="{{ route('registrar.pending') }}" class="nav-link {{ Request::routeIs('registrar.pending') || Request::routeIs('registrar.verify') ? 'active' : '' }}">PENDING VERIFICATION</a>
            @endif

            {{-- 3. STUDENT ONLY --}}
            @if(Auth::user()->role === 'Student')
                <a href="{{ route('enrollments.index') }}" class="nav-link {{ Request::routeIs('enrollments.index') ? 'active' : '' }}">MY RECORDS</a>
                <a href="{{ route('enrollments.step3') }}" class="nav-link {{ Request::routeIs('enrollments.step3') ? 'active' : '' }}">ENROLL NOW</a>
            @endif

            {{-- 4. SHARED MANAGEMENT (Admin and Registrar) --}}
            @can('registrar-access') 
                <a href="{{ route('admin.manage_enrollments') }}" class="nav-link {{ Request::is('records*') ? 'active' : '' }}">MANAGE ENROLLMENTS</a>
            @endcan

            {{-- CLEAN ADD DROPDOWN FOR ADMIN/REGISTRAR --}}
            @can('admin-only')
                <div class="dropdown">
                    <a href="#" class="nav-link">ADD +</a>
                    <div class="dropdown-menu">
                        {{-- Moved from main navbar --}}
                        <a class="dropdown-item" href="{{ route('admin.users.create') }}">Register Student</a>
                        <a class="dropdown-item" href="{{ route('admin.add_subject') }}">Add Subject</a>
                        
                        <div class="dropdown-divider"></div>
                        
                        {{-- New Add Forms --}}
                        <a class="dropdown-item" href="{{ route('admin.courses.create') }}">Add Course</a>
                        <a class="dropdown-item" href="{{ route('admin.departments.create') }}">Add Department</a>
                        <a class="dropdown-item" href="{{ route('admin.instructors.create') }}">Add Instructor</a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a class="dropdown-item" href="{{ route('admin.sections.create') }}">Create Section / Schedule</a>
                    </div>
                </div>
            @endcan

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('logout') }}" style="margin-left: auto;">
                @csrf
                <button type="submit" class="logout-btn">LOGOUT ({{ Auth::user()->name }})</button>
            </form>
        @else
            <div style="margin-left: auto; display: flex; gap: 15px;">
                <a href="{{ route('login') }}" class="nav-link {{ Request::is('login') ? 'active' : '' }}">LOGIN</a>
            </div>
        @endauth
    </nav>

        <main>
            {{-- Content is rendered here --}}
            @yield('content')
        </main>

        @stack('scripts')
    </body>
    </html>