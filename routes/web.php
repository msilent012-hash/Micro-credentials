<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('Homepage'))->name('Homepage');
Route::get('/login', fn() => view('login'))->name('login');
Route::get('/register', fn() => view('register'))->name('register');
// --- TEMPORARY: stub any named routes the dashboard view calls but you
// haven't built yet, so it doesn't crash with RouteNotFoundException.
// Remove this whole block once your real routes exist. ---
foreach ([
    'home', 'courses.index', 'courses.show',
    'profile.edit',
    'notifications.index', 'search',
] as $name) {
    if (! Route::has($name)) {
        Route::get('/_stub/'.$name, fn () => '')->name($name);
    }
}

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
        'badges' => [],
    ]);
})->name('dashboard');

// --- Browse Courses page with dummy data ---
Route::get('/courses/browse', function () {
    return view('Student_browse_Courses', [   // <-- updated to match your renamed file
        'user' => (object) ['name' => 'Ana'],
        'courses' => collect([
            (object) [
                'id' => 1, 'title' => 'Full - Stack Web Development with Laravel',
                'description' => 'Master modern web development using Laravel framework, MySQL and Blade templating. Build real-world applications from scratch.',
                'category' => 'Web Development', 'level' => 'Intermediate',
                'instructor' => 'Prof. Juan Dela Cruz', 'duration' => '40h',
                'lessons_count' => 4, 'thumbnail_url' => null,
            ],
            (object) [
                'id' => 2, 'title' => 'Computer Networking Fundamentals',
                'description' => 'Master modern web development using Laravel framework, MySQL and Blade templating. Build real-world applications from scratch.',
                'category' => 'Web Development', 'level' => 'Intermediate',
                'instructor' => 'Prof. Juan Dela Cruz', 'duration' => '40h',
                'lessons_count' => 4, 'thumbnail_url' => null,
            ],
        ]),
        'categories' => ['Web Development', 'Networking'],
        'levels' => ['Beginner', 'Intermediate', 'Advanced'],
    ]);
})->name('courses.browse');
Route::get('/badges', function () {
    return view('Student_MyBadges', [   // <-- updated to match the new file name
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
            (object) ['title' => 'AWS Certified Solutions Architect', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA A+', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA Network+', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'CompTIA Security+', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Cisco CCNA', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Microsoft Certified: Azure Fundamentals', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Oracle Certified Professional: Java SE', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Google IT Support Professional', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Certified Kubernetes Administrator', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'PMP - Project Management Professional', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Certified Ethical Hacker (CEH)', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
            (object) ['title' => 'Microsoft Certified: Azure Developer Associate', 'icon_url' => null, 'view_url' => '#', 'download_url' => '#'],
        ]),
    ]);
})->name('certificates.index');
Route::get('/profile', function () {
    return view('Student_Profile', [
        'user' => (object) [
            'name' => 'Ana Maria',
            'role' => 'Student',
            'phone' => '09345678912',
            'email' => 'Ana123@gmail.com',
            'joined_at' => \Carbon\Carbon::parse('2021-01-15'),
            'location' => 'San, Juan, Pangasinan',
            'avatar_url' => null,
            'about' => 'Passionate about Web Development and App Development. Problem Solving Love to Learn new Skills and More',
            'date_of_birth' => 'October 12, 2003',
            'gender' => 'Male',
            'education' => 'BS Information Technology',
            'bio' => 'Photo Editing Skilled. Frontend Web Designer for Wordpress. Can Edit Multiple Frames in Just 1 Hour',
            'language' => 'English',
            'timezone' => '(GMT+8:00) Asia/Manila',
        ],
        'progress' => ['completed' => 12, 'total' => 20],
        'achievements' => [],
        'activities' => [],
    ]);
})->name('profile.show');
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
            'destination' => 'Full Stack Web Developer',
            'destination_color' => '#5FD93D',
            'connector_to_destination' => '#2563EB',
        ],
        'recommendations' => [
            ['title' => 'Take Blade Courses', 'completion' => 0],
            ['title' => 'Take SQL Courses',   'completion' => 0],
            ['title' => 'Networking Course',  'completion' => 0],
            ['title' => 'HTML & CSS',         'completion' => 0],
        ],
        'desiredPathway' => [
            'title' => 'Data Analyst',
            'current_competencies' => ['Networking Course', 'HTML & CSS'],
            'missing_competencies' => ['Python', 'Statistics'],
        ],
        'readinessPercent' => 60,
        'readinessLabel' => 'Data Analytics',
    ]);
})->name('pathways.index');
Route::get('/analytics', function () {
    return view('Student_Analytics', [
        'user' => (object) ['name' => 'Ana'],
        'stats' => [
            'active_courses' => 2,
            'badges_earned'  => 4,
            'score_avg'      => 99.7,
            'hours_enrolled' => 14,
        ],
        'activeCourses' => collect([
            (object) ['title' => 'Introduction to Artificial Intelligence', 'meta' => '35 Students · 4 Faculty', 'thumbnail_url' => null, 'percent' => 62],
            (object) ['title' => 'Database Fundamentals', 'meta' => '45 Students · 2 Faculty', 'thumbnail_url' => null, 'percent' => 70],
            (object) ['title' => 'Introduction to Artificial Intelligence', 'meta' => '38 Students · 4 Faculty', 'thumbnail_url' => null, 'percent' => 85],
            (object) ['title' => 'Database Fundamentals', 'meta' => '43 Students · 2 Faculty', 'thumbnail_url' => null, 'percent' => 62],
        ]),
        'recentBadges' => collect([
            (object) ['name' => 'Database Earned', 'earned_count' => 61],
            (object) ['name' => 'AI Pioneer', 'earned_count' => 28],
            (object) ['name' => 'Web Wizard', 'earned_count' => 38],
            (object) ['name' => 'Certified Pro', 'earned_count' => 15],
        ]),
        'enrollmentByCourse' => collect([
            (object) ['label' => 'Database Fund', 'value' => 63, 'percent' => 63],
            (object) ['label' => 'Web Dev Boot', 'value' => 57, 'percent' => 57],
            (object) ['label' => 'Intro to AI', 'value' => 48, 'percent' => 48],
            (object) ['label' => 'ML Essentials', 'value' => 34, 'percent' => 34],
        ]),
        'completionRate' => collect([
            (object) ['label' => 'Database Fund', 'value' => 91, 'percent' => 91],
            (object) ['label' => 'Web Dev Boot', 'value' => 84, 'percent' => 84],
            (object) ['label' => 'Intro to AI', 'value' => 77, 'percent' => 77],
            (object) ['label' => 'ML Essentials', 'value' => 62, 'percent' => 62],
        ]),
    ]);
})->name('analytics.index');