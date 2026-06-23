<?php

use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════════════
// GLOBAL VIEW COMPOSER
// Runs on every view render and merges saved profile data (avatar, name,
// role) from the session into the $user object — so the topbar avatar and
// username stay up-to-date on ALL pages automatically.
// ══════════════════════════════════════════════════════════════════════════
app('view')->composer('*', function (\Illuminate\View\View $view) {
    try {
        $saved = session('profile_data', []);
    } catch (\Throwable $e) {
        return; // session not available yet (e.g. during boot)
    }

    if (empty($saved)) return;

    $data = $view->getData();
    if (! isset($data['user'])) return;

    $user = $data['user'];

    // Merge whichever fields were saved in the session
    foreach (['name', 'role', 'avatar_url', 'email', 'phone', 'location'] as $field) {
        if (array_key_exists($field, $saved) && ! empty($saved[$field])) {
            $user->$field = $saved[$field];
        }
    }

    $view->with('user', $user);
});

Route::get('/', fn() => view('Homepage'))->name('Homepage');
Route::get('/login', fn() => view('login'))->name('login');
Route::get('/register', fn() => view('register'))->name('register');

// --- TEMPORARY stubs (remove once real routes exist) ---
foreach ([
    'home',
    'profile.edit',
    'notifications.index', 'search',
] as $name) {
    if (! Route::has($name)) {
        Route::get('/_stub/'.$name, fn () => '')->name($name);
    }
}

// ✅ "Courses" nav button now goes to Browse Courses instead of blank stub
Route::get('/courses', fn() => redirect()->route('courses.browse'))->name('courses.index');

Route::get('/preview-dashboard', function () {
    return view('Student_dashboard', [
        'user' => (object) ['name' => 'Ana'],
        'stats' => [
            'active_courses' => 2,
            'completed'      => 1,
            'badges_earned'  => 3,
            'certificates'   => 1,
        ],
        'courses' => collect([
            (object) ['id' => 1, 'title' => 'Full-Stack Web Dev with Laravel', 'category' => null, 'thumbnail_url' => null, 'progress_percent' => 0],
            (object) ['id' => 2, 'title' => 'Introduction to AI', 'category' => 'Artificial Intelligence', 'thumbnail_url' => null, 'progress_percent' => 0],
        ]),
        'progress' => [],
        'badges'   => [],
    ]);
})->name('dashboard');

// --- Browse Courses ---
Route::get('/courses/browse', function () {
    return view('Student_browse_Courses', [
        'user' => (object) ['name' => 'Ana'],
        'courses' => collect([
            (object) [
                'id' => 1, 'title' => 'Full - Stack Web Development with Laravel',
                'description'   => 'Master modern web development using Laravel framework, MySQL and Blade templating. Build real-world applications from scratch.',
                'category'      => 'Web Development', 'level' => 'Intermediate',
                'instructor'    => 'Prof. Juan Dela Cruz', 'duration' => '40h',
                'lessons_count' => 4, 'thumbnail_url' => null,
            ],
            (object) [
                'id' => 2, 'title' => 'Computer Networking Fundamentals',
                'description'   => 'Master modern web development using Laravel framework, MySQL and Blade templating. Build real-world applications from scratch.',
                'category'      => 'Web Development', 'level' => 'Intermediate',
                'instructor'    => 'Prof. Juan Dela Cruz', 'duration' => '40h',
                'lessons_count' => 4, 'thumbnail_url' => null,
            ],
        ]),
        'categories' => ['Web Development', 'Networking'],
        'levels'     => ['Beginner', 'Intermediate', 'Advanced'],
        'filters'    => ['q' => null, 'category' => null, 'level' => null],
    ]);
})->name('courses.browse');

// ✏️  NEW: Single course description page
Route::get('/courses/{id}', function ($id) {
    // Dummy courses (same objects as browse, with extra fields)
    $allCourses = [
        1 => (object) [
            'id'             => 1,
            'title'          => 'Full - Stack Web Development with Laravel',
            'description'    => 'Explore the fundamentals of Artificial Intelligence, machine learning algorithms and their real-world applications.',
            'category'       => 'Web Development',
            'level'          => 'Intermediate',
            'is_featured'    => true,
            'instructor'     => 'Prof. Juan Dela Cruz',
            'duration'       => '40h',
            'lessons_count'  => 2,
            'enrolled_count' => 4,
            'thumbnail_url'  => null,
            'passing_score'  => 75,
            'skills'         => ['Laravel', 'PHP', 'MySQL', 'MVC'],
            'objectives'     => [
                'Build Full-web Applications',
                'Understand MVC Architecture',
                'Master Laravel Eloquent ORM',
                'Deploy applications in production',
            ],
        ],
        2 => (object) [
            'id'             => 2,
            'title'          => 'Computer Networking Fundamentals',
            'description'    => 'Learn the core concepts of computer networking, protocols, and network design.',
            'category'       => 'Networking',
            'level'          => 'Beginner',
            'is_featured'    => false,
            'instructor'     => 'Prof. Juan Dela Cruz',
            'duration'       => '30h',
            'lessons_count'  => 3,
            'enrolled_count' => 10,
            'thumbnail_url'  => null,
            'passing_score'  => 75,
            'skills'         => ['TCP/IP', 'DNS', 'Routing', 'Switching'],
            'objectives'     => [
                'Understand OSI and TCP/IP models',
                'Configure basic network devices',
                'Troubleshoot common network issues',
            ],
        ],
    ];

    $course = $allCourses[$id] ?? $allCourses[1]; // fallback to course 1

    return view('Student_Course_Description', [
        'user'              => (object) ['name' => 'Ana'],
        'course'            => $course,
        'instructor_detail' => (object) [
            'name'       => 'Prof. Juan Dela Cruz',
            'department' => 'College of Information Technology',
            'bio'        => 'Senior Faculty Member specializing in web development using Laravel.',
            'avatar_url' => null,
        ],
        'modules' => collect([
            (object) ['title' => 'Database & Eloquent ORM',   'description' => 'Master database interactions with Eloquent', 'type' => 'Test',  'duration' => '20m'],
            (object) ['title' => 'Migrations and Schema',     'description' => 'Video: 15m',                                 'type' => 'Video', 'duration' => '20m'],
            (object) ['title' => 'Eloquent Relationships',    'description' => 'Test: 25m',                                  'type' => 'Test',  'duration' => '25m'],
        ]),
        'quiz' => (object) [
            'id'              => 1,
            'title'           => 'Database & Eloquent ORM',
            'questions_count' => 3,
            'passing_score'   => 75,
        ],
        'is_enrolled'      => false,   // false = show "Enroll Now" button
        'progress_percent' => 0,
    ]);
})->name('courses.show');

// Enroll POST → redirect to the enrollment/learning page
Route::post('/courses/{id}/enroll', function ($id) {
    // TODO: real enrollment logic (e.g. DB insert) goes here
    return redirect()->route('courses.learn', $id);
})->name('courses.enroll');

// ── Course Enrollment / Learning Page ──────────────────────────────────────
Route::get('/courses/{id}/learn', function ($id) {

    // Dummy modules — each has an id, title, lessons[], and optional quiz
    $modules = collect([
        (object) [
            'id'      => 1,
            'title'   => 'Laravel Fundamentals',
            'lessons' => collect([
                (object) ['id' => 1, 'title' => 'Introduction to Laravel',  'type' => 'Video', 'duration' => '15m', 'thumbnail_url' => null],
                (object) ['id' => 2, 'title' => 'Routing and Controllers',  'type' => 'Text',  'duration' => '15m', 'thumbnail_url' => null],
                (object) ['id' => 3, 'title' => 'Blade Templates',          'type' => 'Text',  'duration' => '15m', 'thumbnail_url' => null],
            ]),
            'quiz' => (object) [
                'id'              => 1,
                'title'           => 'Laravel Fundamentals Quiz',
                'questions_count' => 3,
                'passing_score'   => 75,
            ],
        ],
        (object) [
            'id'      => 2,
            'title'   => 'Database & Eloquent ORM',
            'lessons' => collect([
                (object) ['id' => 4, 'title' => 'Database Migrations',       'type' => 'Video', 'duration' => '20m', 'thumbnail_url' => null],
                (object) ['id' => 5, 'title' => 'Eloquent Relationships',    'type' => 'Text',  'duration' => '25m', 'thumbnail_url' => null],
            ]),
            'quiz' => (object) [
                'id'              => 2,
                'title'           => 'Database & Eloquent ORM Quiz',
                'questions_count' => 3,
                'passing_score'   => 75,
            ],
        ],
    ]);

    $allCourses = [
        1 => (object) ['id' => 1, 'title' => 'Full - Stack Web Development with Laravel', 'category' => 'Web Development', 'thumbnail_url' => null, 'progress_percent' => 0],
        2 => (object) ['id' => 2, 'title' => 'Computer Networking Fundamentals',          'category' => 'Networking',       'thumbnail_url' => null, 'progress_percent' => 0],
    ];

    $course        = $allCourses[$id] ?? $allCourses[1];
    $currentLesson = $modules->first()?->lessons->first();   // start at lesson 1
    $currentModule = $modules->first();
    $totalLessons  = $modules->sum(fn($m) => $m->lessons->count());

    return view('Student_Course_Enrollment', [
        'user'             => (object) ['name' => 'Ana'],
        'course'           => $course,
        'modules'          => $modules,
        'current_lesson'   => $currentLesson,
        'current_module'   => $currentModule,
        'total_lessons'    => $totalLessons,
        'badge_count'      => 1,
        'progress_percent' => 0,
    ]);
})->name('courses.learn');

// Individual lesson view (stub — reuses enrollment page for now)
Route::get('/courses/{courseId}/lesson/{lessonId}', function ($courseId, $lessonId) {
    return redirect()->route('courses.learn', $courseId);
})->name('courses.lesson');

// Quiz stub
Route::get('/quiz/{id}', fn($id) => redirect()->route('dashboard'))->name('quiz.show');

Route::get('/badges', function () {
    return view('Student_MyBadges', [
        'user' => (object) ['name' => 'Ana'],
        'stats' => [
            'active_courses' => 2,
            'completed'      => 1,
            'badges_earned'  => 3,
            'certificates'   => 1,
        ],
        'badges' => collect([
            (object) ['name' => 'Database Master', 'description' => 'Completed Database and Eloquent Model', 'icon_url' => null, 'earned_at' => now()->subHours(17)],
            (object) ['name' => 'Database Master', 'description' => 'Completed Database and Eloquent Model', 'icon_url' => null, 'earned_at' => now()->subHours(17)],
            (object) ['name' => 'Database Master', 'description' => 'Completed Database and Eloquent Model', 'icon_url' => null, 'earned_at' => now()->subHours(17)],
            (object) ['name' => 'Database Master', 'description' => 'Completed Database and Eloquent Model', 'icon_url' => null, 'earned_at' => now()->subHours(17)],
        ]),
    ]);
})->name('badges.index');

Route::get('/certificates', function () {
    return view('Student_Certificates', [
        'user' => (object) ['name' => 'Ana'],
        'certificates' => collect([
            (object) ['title' => 'AWS Certified Solutions Architect',              'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA A+',                                     'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA Network+',                               'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA Security+',                              'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Cisco CCNA',                                     'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Microsoft Certified: Azure Fundamentals',        'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Oracle Certified Professional: Java SE',         'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Google IT Support Professional',                 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Certified Kubernetes Administrator',             'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'PMP - Project Management Professional',          'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Certified Ethical Hacker (CEH)',                 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Microsoft Certified: Azure Developer Associate', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
        ]),
    ]);
})->name('certificates.index');

// ── Profile ────────────────────────────────────────────────────────────────

Route::get('/profile', function () {

    // ── Default / seed data ───────────────────────────────────────────────
    $defaults = [
        'name'          => 'Ana Maria',
        'role'          => 'Student',
        'phone'         => '09345678912',
        'email'         => 'Ana123@gmail.com',
        'joined_at'     => \Carbon\Carbon::parse('2021-01-15'),   // always Carbon
        'location'      => 'San, Juan, Pangasinan',
        'avatar_url'    => null,
        'about'         => 'Passionate about Web Development and App Development. Problem Solving Love to Learn new Skills and More',
        'date_of_birth' => 'October 12, 2003',
        'gender'        => 'Male',
        'education'     => 'BS Information Technology',
        'bio'           => 'Photo Editing Skilled. Frontend Web Designer for Wordpress. Can Edit Multiple Frames in Just 1 Hour',
        'language'      => 'English',
        'timezone'      => 'Asia/Manila',
    ];

    // ── Merge with any session-persisted edits ────────────────────────────
    $saved  = session('profile_data', []);
    $merged = array_merge($defaults, $saved);

    // joined_at must always be a Carbon instance (never from form input)
    $merged['joined_at'] = $defaults['joined_at'];

    // Normalise date_of_birth to a human-readable format for display
    if (!empty($merged['date_of_birth'])) {
        try {
            $merged['date_of_birth'] = \Carbon\Carbon::parse($merged['date_of_birth'])->format('F j, Y');
        } catch (\Throwable $e) { /* keep as-is if it can't be parsed */ }
    }

    return view('Student_Profile', [
        'user'         => (object) $merged,
        'progress'     => ['completed' => 12, 'total' => 20],
        'achievements' => [],
        'activities'   => [],
    ]);
})->name('profile.show');

// ✅ Handle Edit Profile form — persists to session until DB is wired up
Route::patch('/profile', function (\Illuminate\Http\Request $request) {

    // Start from what was already in session so untouched fields are kept
    $existing = session('profile_data', []);

    // Overwrite with every field the form sent
    $updated = array_merge($existing, $request->only([
        'name', 'role', 'phone', 'location',
        'about', 'date_of_birth', 'gender', 'education', 'bio',
        'email', 'language', 'timezone',
    ]));

    // ── Avatar ─────────────────────────────────────────────────────────────
    // JS resized the image client-side and stored it as a base64 data-URL in
    // the hidden "avatar_base64" field — so no PHP file-upload limits apply.
    if ($request->filled('avatar_base64')) {
        $dataUrl = $request->input('avatar_base64');
        // Basic sanity check: must be an image data-URL
        if (str_starts_with($dataUrl, 'data:image/')) {
            $updated['avatar_url'] = $dataUrl;
        }
    }

    // Persist the full updated profile to session
    session(['profile_data' => $updated]);

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');

    // ── TODO (when DB is ready) ────────────────────────────────────────────
    // Replace the two lines above with:
    //   $user = \Illuminate\Support\Facades\Auth::user();
    //   $user->fill($updated)->save();
    //   return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
})->name('profile.update');

// ──────────────────────────────────────────────────────────────────────────

Route::get('/pathways', function () {
    return view('Student_MyPathways', [
        'user' => (object) ['name' => 'Ana'],
        'pathway' => [
            'steps' => [
                ['label' => 'Goal 1', 'title' => 'Web Development', 'color' => '#2DD4CF', 'status' => 'completed'],
                ['label' => 'Goal 2', 'title' => 'Laravel',         'color' => '#D8C84A', 'status' => 'completed'],
                ['label' => 'Goal 3', 'title' => 'SQL',             'color' => '#E5483D', 'status' => 'current'],
                ['label' => 'Goal 4', 'title' => 'Locked',          'color' => '#9CA3AF', 'status' => 'locked'],
            ],
            'destination'               => 'Full Stack Web Developer',
            'destination_color'         => '#5FD93D',
            'connector_to_destination'  => '#2563EB',
        ],
        'recommendations' => [
            ['title' => 'Take Blade Courses', 'completion' => 0],
            ['title' => 'Take SQL Courses',   'completion' => 0],
            ['title' => 'Networking Course',  'completion' => 0],
            ['title' => 'HTML & CSS',         'completion' => 0],
        ],
        'desiredPathway' => [
            'title'                  => 'Data Analyst',
            'current_competencies'   => ['Networking Course', 'HTML & CSS'],
            'missing_competencies'   => ['Python', 'Statistics'],
        ],
        'readinessPercent' => 60,
        'readinessLabel'   => 'Data Analytics',
    ]);
})->name('pathways.index');

Route::get('/analytics', function () {
    return view('Student_Analytics', [
        'user' => (object) ['name' => 'Ana'],
        'stats' => [
            'active_courses'  => 2,
            'badges_earned'   => 4,
            'score_avg'       => 99.7,
            'hours_enrolled'  => 14,
        ],
        'activeCourses' => collect([
            (object) ['title' => 'Introduction to Artificial Intelligence', 'meta' => '35 Students · 4 Faculty', 'thumbnail_url' => null, 'percent' => 62],
            (object) ['title' => 'Database Fundamentals',                   'meta' => '45 Students · 2 Faculty', 'thumbnail_url' => null, 'percent' => 70],
            (object) ['title' => 'Introduction to Artificial Intelligence', 'meta' => '38 Students · 4 Faculty', 'thumbnail_url' => null, 'percent' => 85],
            (object) ['title' => 'Database Fundamentals',                   'meta' => '43 Students · 2 Faculty', 'thumbnail_url' => null, 'percent' => 62],
        ]),
        'recentBadges' => collect([
            (object) ['name' => 'Database Earned', 'earned_count' => 61],
            (object) ['name' => 'AI Pioneer',      'earned_count' => 28],
            (object) ['name' => 'Web Wizard',      'earned_count' => 38],
            (object) ['name' => 'Certified Pro',   'earned_count' => 15],
        ]),
        'enrollmentByCourse' => collect([
            (object) ['label' => 'Database Fund', 'value' => 63, 'percent' => 63],
            (object) ['label' => 'Web Dev Boot',  'value' => 57, 'percent' => 57],
            (object) ['label' => 'Intro to AI',   'value' => 48, 'percent' => 48],
            (object) ['label' => 'ML Essentials', 'value' => 34, 'percent' => 34],
        ]),
        'completionRate' => collect([
            (object) ['label' => 'Database Fund', 'value' => 91, 'percent' => 91],
            (object) ['label' => 'Web Dev Boot',  'value' => 84, 'percent' => 84],
            (object) ['label' => 'Intro to AI',   'value' => 77, 'percent' => 77],
            (object) ['label' => 'ML Essentials', 'value' => 62, 'percent' => 62],
        ]),
    ]);
})->name('analytics.index');