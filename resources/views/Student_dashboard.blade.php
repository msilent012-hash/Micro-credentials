{{--
    resources/views/dashboard.blade.php

    Single self-contained Blade view (no layout file needed).

    Expected data from the controller, e.g.:

    return view('dashboard', [
        'user'   => $user,                 // Auth::user() - needs ->name
        'stats'  => [
            'active_courses' => $activeCoursesCount,
            'completed'      => $completedCount,
            'badges_earned'  => $badgesEarnedCount,
            'certificates'   => $certificatesCount,
        ],
        'courses'  => $enrolledCourses,     // collection of Course models
        'progress' => $progressItems,       // collection for the "Progress" side panel
        'badges'   => $earnedBadges,        // collection for the "Badges" side panel
    ]);

    Each $course is expected to expose:
        ->id, ->title, ->category, ->thumbnail_url, ->progress_percent
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Upskill</title>
<style>
    :root{
        --navy:#13176b;
        --navy-deep:#0c0f4d;
        --gold:#dba617;
        --gold-dark:#c4930f;
        --cyan:#7fe9e3;
        --thumb:#d8e3f8;
        --ink:#13176b;
        --muted:#6b7280;
        --line:#e5e7eb;
        --shadow: 0 10px 25px rgba(19,23,107,0.08);
    }
    *{box-sizing:border-box;}
    body{font-family:"Segoe UI", Roboto, Helvetica, Arial, sans-serif;color:var(--ink);margin:0;background:#fff;}
    a{text-decoration:none;color:inherit;}
    button{font-family:inherit;cursor:pointer;}

    /* Topbar */
    .topbar{background:var(--navy);display:flex;align-items:center;justify-content:space-between;padding:14px 28px;gap:20px;}
    .brand{display:flex;align-items:center;gap:14px;color:#fff;white-space:nowrap;}
    .brand .logo{width:46px;height:46px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .brand .logo svg{width:30px;height:30px;}
    .brand .logo img{width:100%;height:100%;object-fit:contain;border-radius:50%;padding:3px;}
    .brand h1{font-size:24px;letter-spacing:1px;margin:0;font-weight:800;}
    .nav-pills{display:flex;gap:14px;flex-wrap:wrap;margin-left:auto;}
    .nav-pills a{background:#fff;color:var(--navy);font-weight:700;padding:10px 26px;border-radius:999px;font-size:15px;}
    .nav-pills a.is-active{outline:2px solid var(--cyan);}
    .search-box{display:flex;align-items:center;gap:10px;background:#fff;border-radius:999px;padding:10px 18px;min-width:240px;color:var(--muted);}
    .search-box input{border:none;outline:none;font-size:15px;width:100%;color:var(--ink);background:transparent;}
    .icon-cluster{display:flex;align-items:center;gap:14px;}
    .icon-circle{width:42px;height:42px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;}
    .icon-circle svg{width:22px;height:22px;color:var(--navy);}

    /* Layout */
    .layout{display:grid;grid-template-columns:230px 1fr;min-height:calc(100vh - 74px);}

    /* Sidebar */
    .sidebar{border-right:1px solid var(--line);padding:28px 16px;display:flex;flex-direction:column;gap:6px;}
    .side-link{display:flex;align-items:center;gap:14px;padding:14px;border-radius:14px;font-weight:700;font-size:16px;color:var(--navy);}
    .side-link svg{width:26px;height:26px;flex-shrink:0;}
    .side-link.active{background:var(--navy);color:#fff;}
    .side-icon-box{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .side-link.active .side-icon-box svg{color:#fff;width:26px;height:26px;}
    .side-link:not(.active) .side-icon-box svg{color:var(--navy);width:26px;height:26px;}

    /* Main */
    .main{padding:32px 36px 60px;}
    .page-head{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:28px;}
    .page-head h2{font-size:30px;margin:0 0 6px;color:var(--navy);}
    .page-head p{margin:0;color:var(--muted);font-size:15px;}
    .btn-outline{background:#fff;border:1.5px solid #c9ccdb;color:#9aa0b4;font-weight:600;padding:12px 24px;border-radius:10px;font-size:15px;}

    /* Stats */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;margin-bottom:36px;}
    .stat-card{border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:26px 20px;text-align:center;}
    .stat-top{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:18px;}
    .stat-top svg{width:46px;height:46px;color:var(--navy);}
    .stat-top .num{font-size:44px;font-weight:800;color:var(--navy);}
    .stat-card .label{font-weight:800;font-size:19px;color:var(--navy);}

    /* Content grid */
    .content-grid{display:grid;grid-template-columns:1fr 360px;gap:28px;}
    .courses-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
    .courses-head h3{font-size:24px;margin:0;color:var(--navy);}
    .courses-head .enroll-more{font-weight:700;color:var(--navy);font-size:17px;}

    .course-card{border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);padding:18px;display:flex;align-items:center;gap:18px;margin-bottom:20px;}
    .thumb{width:78px;height:78px;border-radius:12px;background:var(--thumb);flex-shrink:0;background-size:cover;background-position:center;}
    .course-info{flex:1;min-width:0;}
    .course-info h4{margin:0 0 4px;font-size:17px;color:var(--navy);}
    .course-info .cat{font-size:13px;color:var(--muted);margin-bottom:8px;}
    .progress-track{flex:1;height:7px;border-radius:999px;background:#e3e6f0;overflow:hidden;}
    .progress-fill{height:100%;background:var(--navy);border-radius:999px;}
    .pct{font-size:12px;color:var(--muted);margin-top:6px;}
    .btn-start{background:#fff;border:1.5px solid var(--navy);color:var(--navy);font-weight:700;padding:10px 22px;border-radius:10px;font-size:15px;flex-shrink:0;}
    .empty-state{border:1px dashed var(--line);border-radius:16px;padding:30px;text-align:center;color:var(--muted);}

    /* Side panels */
    .panel{border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);overflow:hidden;margin-bottom:24px;min-height:230px;}
    .panel-head{background:var(--gold);color:var(--navy);font-weight:800;font-size:22px;padding:14px 22px;display:flex;align-items:center;justify-content:space-between;}
    .panel-body{padding:18px 22px;color:var(--muted);font-size:14px;}
    .panel-body ul{margin:0;padding-left:18px;}
    .panel-body li{margin-bottom:8px;}

    @media (max-width:980px){
        .layout{grid-template-columns:1fr;}
        .sidebar{flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--line);}
        .stats{grid-template-columns:repeat(2,1fr);}
        .content-grid{grid-template-columns:1fr;}
    }
</style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <span class="logo">
            <img src="{{ asset('images/PSU-Logo.png') }}" alt="PSU Logo">
        </span>
        <h1>UPSKILL</h1>
    </div>

    <nav class="nav-pills">
        <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'is-active' : '' }}">Courses</a>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
    </nav>

    <form action="{{ route('search') }}" method="GET" class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="q" placeholder="Search" value="{{ request('q') }}">
    </form>

    <div class="icon-cluster">
        <a href="{{ route('notifications.index') }}" class="icon-circle" title="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a href="{{ route('profile.show') }}"
           class="icon-circle"
           title="{{ $user->name ?? 'Profile' }}"
           @if($user->avatar_url ?? null)
               style="background-image:url('{{ $user->avatar_url }}');background-size:cover;background-position:center;overflow:hidden;"
           @endif>
            @unless($user->avatar_url ?? null)
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
            @endunless
        </a>
    </div>
</header>

<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
            </span>
            Dashboard
        </a>
        <a href="{{ route('courses.browse') }}" class="side-link {{ request()->routeIs('courses.browse') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            Browse Courses
        </a>
        <a href="{{ route('badges.index') }}" class="side-link {{ request()->routeIs('badges.*') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6 7 1-5 5 1.5 7L12 17l-6.5 4L7 14 2 9l7-1 3-6z"/></svg>
            </span>
            My Badges
        </a>
        <a href="{{ route('certificates.index') }}" class="side-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="5"/><path d="M8 13l-2 8 6-3 6 3-2-8"/></svg>
            </span>
            Certificates
        </a>
        <a href="{{ route('profile.show') }}" class="side-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
            </span>
            Profile
        </a>
        <a href="{{ route('pathways.index') }}" class="side-link {{ request()->routeIs('pathways.*') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="12" r="3"/><path d="M6 9v6"/><path d="M8.5 7.5L15.5 10.5"/><path d="M8.5 16.5L15.5 13.5"/></svg>
            </span>
            My Pathways
        </a>
        <a href="{{ route('analytics.index') }}" class="side-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <span class="side-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><rect x="7" y="13" width="3" height="5"/><rect x="12" y="9" width="3" height="9"/><rect x="17" y="6" width="3" height="12"/></svg>
            </span>
            Analytics
        </a>
    </aside>

    {{-- Main content --}}
    <main class="main">

        <div class="page-head">
            <div>
                <h2>Welcome, {{ $user->name ?? 'Student' }}!</h2>
                <p>Track your learning journey and continue where you left off.</p>
            </div>
            <a href="{{ route('courses.browse') }}">
                <button class="btn-outline" type="button">Explore Courses</button>
            </a>
        </div>

        {{-- Stat cards --}}
        <section class="stats">
            <div class="stat-card">
                <div class="stat-top">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 2h12v20l-6-4-6 4V2z"/></svg>
                    <span class="num">{{ $stats['active_courses'] ?? 0 }}</span>
                </div>
                <div class="label">Active Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="var(--navy)"/><path d="M8 12.5l2.5 2.5L16 9.5" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="num">{{ $stats['completed'] ?? 0 }}</span>
                </div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="num">{{ $stats['badges_earned'] ?? 0 }}</span>
                </div>
                <div class="label">Badges Earned</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 2l3 6 7 1-5 5 1.5 7L12 17l-6.5 4L7 14 2 9l7-1 3-6z" fill="var(--navy)"/><path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="num">{{ $stats['certificates'] ?? 0 }}</span>
                </div>
                <div class="label">Certificates</div>
            </div>
        </section>

        {{-- Courses + side panels --}}
        <div class="content-grid">

            <section class="courses-col">
                <div class="courses-head">
                    <h3>My Courses</h3>
                    <a href="{{ route('courses.browse') }}" class="enroll-more">+ Enroll More</a>
                </div>

                @forelse ($courses as $course)
                    <div class="course-card">
                        <div class="thumb" @if($course->thumbnail_url) style="background-image:url('{{ $course->thumbnail_url }}')" @endif></div>
                        <div class="course-info">
                            <h4>{{ $course->title }}</h4>
                            @if($course->category)
                                <div class="cat">{{ $course->category }}</div>
                            @endif
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $course->progress_percent ?? 0 }}%"></div>
                            </div>
                            <div class="pct">{{ $course->progress_percent ?? 0 }}% Complete</div>
                        </div>
                        <a href="{{ route('courses.show', $course->id) }}">
                            <button class="btn-start" type="button">
                                {{ ($course->progress_percent ?? 0) > 0 ? 'Continue' : 'Start' }}
                            </button>
                        </a>
                    </div>
                @empty
                    <div class="empty-state">
                        You haven't enrolled in any courses yet.
                        <br>
                        <a href="{{ route('courses.browse') }}" class="enroll-more">Browse courses to get started</a>
                    </div>
                @endforelse
            </section>

            <aside class="side-col">
                <div class="panel">
                    <div class="panel-head">
                        <span>Progress</span>
                        <a href="{{ route('analytics.index') }}" style="color:inherit;">--&gt;</a>
                    </div>
                    <div class="panel-body">
                        @forelse ($progress ?? [] as $item)
                            <p>{{ $item }}</p>
                        @empty
                            <p>No progress activity yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <span>Badges</span>
                    </div>
                    <div class="panel-body">
                        @forelse ($badges ?? [] as $badge)
                            @php /** @var object{name:string}|string $badge */ @endphp
                            <p>{{ $badge->name ?? $badge }}</p>
                        @empty
                            <p>No badges earned yet.</p>
                        @endforelse
                    </div>
                </div>
            </aside>

        </div>

    </main>
</div>

</body>
</html>