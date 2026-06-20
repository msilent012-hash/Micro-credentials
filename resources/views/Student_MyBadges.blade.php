{{--
    resources/views/my_badges.blade.php

    Expected data from the controller, e.g.:

    return view('my_badges', [
        'user'  => $user,                  // Auth::user() - needs ->name
        'stats' => [
            'active_courses' => $activeCoursesCount,
            'completed'      => $completedCount,
            'badges_earned'  => $badgesEarnedCount,
            'certificates'   => $certificatesCount,
        ],
        'badges' => $recentlyEarnedBadges,  // collection, most recent first
    ]);

    Each $badge is expected to expose:
        ->name, ->description, ->icon_url, ->earned_at (a Carbon instance, for ->diffForHumans())
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Badges | Upskill</title>
<style>
    :root{
        --navy:#13176b;
        --navy-deep:#0c0f4d;
        --gold:#dba617;
        --gold-dark:#c4930f;
        --cyan:#7fe9e3;
        --badge-bg:#f7ecc4;
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
    .icon-circle{width:42px;height:42px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;}
    .icon-circle svg{width:22px;height:22px;color:var(--navy);}

    /* Layout */
    .layout{display:grid;grid-template-columns:230px 1fr;min-height:calc(100vh - 74px);}

    /* Sidebar */
    .sidebar{border-right:1px solid var(--line);padding:28px 16px;display:flex;flex-direction:column;gap:6px;}
    .side-link{display:flex;align-items:center;gap:14px;padding:14px;border-radius:14px;font-weight:700;font-size:16px;color:var(--navy);}
    .side-link svg{width:26px;height:26px;flex-shrink:0;}
    .side-link.active{background:var(--navy);color:#fff;}
    .side-icon-box{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .side-icon-box svg{width:26px;height:26px;color:var(--navy);}
    .side-link.active .side-icon-box svg{color:#fff;}

    /* Main */
    .main{padding:32px 36px 60px;}
    .page-head h2{font-size:30px;margin:0 0 6px;color:var(--navy);}
    .page-head p{margin:0 0 28px;color:var(--muted);font-size:15px;}

    /* Stats (shared with dashboard) */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;margin-bottom:40px;}
    .stat-card{border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:26px 20px;text-align:center;}
    .stat-top{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:18px;}
    .stat-top svg{width:46px;height:46px;color:var(--navy);}
    .stat-top .num{font-size:44px;font-weight:800;color:var(--navy);}
    .stat-card .label{font-weight:800;font-size:19px;color:var(--navy);}

    /* Recently Earned */
    .section-head{font-size:24px;margin:0 0 18px;color:var(--navy);}
    .badge-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:24px;}
    .badge-card{background:var(--badge-bg);border-radius:20px;padding:20px;text-align:center;position:relative;box-shadow:var(--shadow);}
    .badge-time{position:absolute;top:16px;right:18px;font-size:12px;color:var(--muted);}
    .badge-icon{width:90px;height:90px;border-radius:16px;background:var(--thumb);margin:28px auto 18px;background-size:cover;background-position:center;}
    .badge-card h4{margin:0 0 10px;color:var(--navy);font-size:18px;font-weight:800;line-height:1.3;}
    .badge-card .desc{color:var(--muted);font-size:13px;margin:0 0 18px;line-height:1.45;}
    .badge-earned-pill{display:inline-block;background:var(--gold);color:var(--navy);font-weight:800;font-size:13px;padding:8px 22px;border-radius:999px;}
    .empty-state{border:1px dashed var(--line);border-radius:16px;padding:40px;text-align:center;color:var(--muted);grid-column:1/-1;}

    @media (max-width:980px){
        .layout{grid-template-columns:1fr;}
        .sidebar{flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--line);}
        .stats{grid-template-columns:repeat(2,1fr);}
        .badge-grid{grid-template-columns:repeat(2,1fr);}
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
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
        <a href="{{ route('courses.browse') }}" class="{{ request()->routeIs('courses.*') ? 'is-active' : '' }}">Courses</a>
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
        <a href="{{ route('profile.show') }}" class="icon-circle" title="{{ $user->name ?? 'Profile' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
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
            <h2>My Badge Collection</h2>
            <p>Badges earned through course completion</p>
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

        {{-- Recently Earned --}}
        <h3 class="section-head">Recently Earned</h3>
        <div class="badge-grid">
            @forelse ($badges as $badge)
                <div class="badge-card">
                    <span class="badge-time">{{ $badge->earned_at?->diffForHumans() ?? '' }}</span>
                    <div class="badge-icon" @if($badge->icon_url) style="background-image:url('{{ $badge->icon_url }}')" @endif></div>
                    <h4>{{ $badge->name }}</h4>
                    @if($badge->description)
                        <p class="desc">{{ $badge->description }}</p>
                    @endif
                    <span class="badge-earned-pill">Earned</span>
                </div>
            @empty
                <div class="empty-state">
                    No badges earned yet. Complete a course to start collecting them.
                </div>
            @endforelse
        </div>

    </main>
</div>

</body>
</html>
