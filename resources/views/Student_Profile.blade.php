{{--
    resources/views/Student_Profile.blade.php

    Expected data from the controller, e.g.:

    return view('Student_Profile', [
        'user' => $user, // Auth::user() with the fields used below
        'stats' => [
            'courses_enrolled' => $coursesEnrolledCount,
            'badges_earned'    => $badgesEarnedCount,
            'certificates'     => $certificatesCount,
            'hours_learned'    => $hoursLearned,
        ],
        'progress' => [
            'completed' => $completedCoursesCount,
            'total'     => $enrolledCoursesCount,
        ],
        'achievements' => $achievements, // collection, can be empty
        'activities'   => $recentActivities, // collection, can be empty
    ]);

    $user is expected to expose:
        ->name, ->role, ->phone, ->email, ->joined_at (Carbon), ->location,
        ->avatar_url, ->about, ->date_of_birth, ->gender, ->education, ->bio,
        ->language, ->timezone
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | Upskill</title>
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
    .sidebar-progress{border:1px solid var(--line);border-radius:16px;padding:18px;margin-top:14px;box-shadow:var(--shadow);}
    .sidebar-progress .sp-label{color:var(--muted);font-size:13px;margin:0 0 6px;}
    .sidebar-progress .sp-value{color:var(--navy);font-size:22px;font-weight:800;margin:0 0 14px;}
    .sidebar-progress a{color:var(--navy);font-weight:700;font-size:14px;display:flex;align-items:center;gap:6px;}

    /* Main */
    .main{padding:32px 36px 60px;}
    .profile-head{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:28px;}
    .profile-head h2{font-size:30px;margin:0 0 6px;color:var(--navy);}
    .profile-head p{margin:0;color:var(--muted);font-size:14px;}
    .btn-outline{background:#fff;border:1.5px solid var(--line);color:var(--navy);font-weight:700;padding:12px 24px;border-radius:10px;font-size:15px;}

    .profile-grid{display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start;}

    .card-block{border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:24px;margin-bottom:24px;}

    /* Profile info card */
    .profile-info{display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;}
    .avatar-circle{width:90px;height:90px;border-radius:50%;border:3px solid var(--navy);display:flex;align-items:center;justify-content:center;flex-shrink:0;background-size:cover;background-position:center;}
    .avatar-circle svg{width:50px;height:50px;color:var(--navy);}
    .profile-meta h3{color:var(--navy);font-size:22px;margin:0 0 12px;}
    .meta-row{display:flex;flex-wrap:wrap;gap:14px 28px;color:var(--muted);font-size:14px;}
    .meta-row span{display:flex;align-items:center;gap:6px;}
    .meta-row svg{width:16px;height:16px;color:var(--navy);flex-shrink:0;}

    /* Section titles */
    .section-title{display:flex;align-items:center;gap:10px;font-weight:800;color:var(--navy);font-size:18px;margin:0 0 14px;}
    .section-title svg{width:20px;height:20px;flex-shrink:0;}
    .about-text{color:var(--muted);font-size:14px;line-height:1.6;margin:0 0 16px;}
    .detail-row{display:flex;justify-content:space-between;gap:16px;padding:10px 0;border-top:1px solid var(--line);font-size:14px;}
    .detail-row:first-of-type{border-top:none;}
    .detail-row .label{color:var(--muted);flex-shrink:0;}
    .detail-row .value{color:var(--navy);font-weight:700;text-align:right;}

    /* Settings card */
    .settings-row{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-top:1px solid var(--line);gap:16px;flex-wrap:wrap;}
    .settings-row:first-of-type{border-top:none;}
    .settings-row .label{display:flex;align-items:center;gap:10px;color:var(--navy);font-weight:700;font-size:14px;flex-shrink:0;min-width:120px;}
    .settings-row .label svg{width:18px;height:18px;}
    .settings-row .value-text{color:var(--muted);font-size:14px;flex:1;min-width:120px;}
    .btn-change{border:1.5px solid var(--line);background:#fff;color:var(--navy);font-weight:700;padding:7px 20px;border-radius:8px;font-size:13px;flex-shrink:0;}
    .settings-select{border:1.5px solid var(--line);border-radius:8px;padding:9px 14px;font-size:14px;color:var(--navy);flex:1;min-width:150px;background:#fff;}

    /* Right column quick stats */
    .quick-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;}
    .quick-card{border:1px solid var(--line);border-radius:14px;padding:18px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow);font-weight:700;color:var(--navy);font-size:14px;}
    .quick-card svg{width:28px;height:28px;flex-shrink:0;}

    .panel-card{border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:18px 20px;margin-bottom:24px;}
    .panel-card-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
    .panel-card-head h4{margin:0;color:var(--navy);font-size:17px;display:flex;align-items:center;gap:8px;}
    .panel-card-head h4 svg{width:18px;height:18px;}
    .panel-card-head a{color:var(--navy);font-size:13px;font-weight:700;}
    .panel-card-body{min-height:70px;color:var(--muted);font-size:13px;}

    @media (max-width:980px){
        .layout{grid-template-columns:1fr;}
        .sidebar{flex-direction:column;border-right:none;border-bottom:1px solid var(--line);}
        .profile-grid{grid-template-columns:1fr;}
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

        <div class="sidebar-progress">
            <p class="sp-label">Your Progress</p>
            <p class="sp-value">{{ $progress['completed'] ?? 0 }} / {{ $progress['total'] ?? 0 }} Courses</p>
            <a href="{{ route('badges.index') }}">
                View My Badges
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M9 6l6 6-6 6"/></svg>
            </a>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="main">

        <div class="profile-head">
            <div>
                <h2>My Profile</h2>
                <p>Manage your Personal Information and Account Preferences</p>
            </div>
            <a href="{{ route('profile.edit') }}">
                <button class="btn-outline" type="button">Edit Profile</button>
            </a>
        </div>

        <div class="profile-grid">

            {{-- Left column --}}
            <div class="profile-col">

                {{-- Profile info card --}}
                <div class="card-block">
                    <div class="profile-info">
                        <div class="avatar-circle" @if($user->avatar_url ?? null) style="background-image:url('{{ $user->avatar_url }}')" @endif>
                            @unless($user->avatar_url ?? null)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
                            @endunless
                        </div>
                        <div class="profile-meta">
                            <h3>{{ $user->name ?? 'Student' }}</h3>
                            <div class="meta-row">
                                @if($user->role ?? null)
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/></svg>
                                        {{ $user->role }}
                                    </span>
                                @endif
                                @if($user->phone ?? null)
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.84.57 2.8.7A2 2 0 0 1 22 16.92z"/></svg>
                                        {{ $user->phone }}
                                    </span>
                                @endif
                                @if($user->email ?? null)
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 6 10-6"/></svg>
                                        {{ $user->email }}
                                    </span>
                                @endif
                                @if($user->joined_at ?? null)
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                        Joined on {{ $user->joined_at->format('M d, Y') }}
                                    </span>
                                @endif
                                @if($user->location ?? null)
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $user->location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- About me card --}}
                <div class="card-block">
                    <h4 class="section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
                        About me
                    </h4>
                    @if($user->about ?? null)
                        <p class="about-text">{{ $user->about }}</p>
                    @endif
                    @if($user->date_of_birth ?? null)
                        <div class="detail-row">
                            <span class="label">Date of Birth:</span>
                            <span class="value">{{ $user->date_of_birth }}</span>
                        </div>
                    @endif
                    @if($user->gender ?? null)
                        <div class="detail-row">
                            <span class="label">Gender:</span>
                            <span class="value">{{ $user->gender }}</span>
                        </div>
                    @endif
                    @if($user->education ?? null)
                        <div class="detail-row">
                            <span class="label">Education:</span>
                            <span class="value">{{ $user->education }}</span>
                        </div>
                    @endif
                    @if($user->bio ?? null)
                        <div class="detail-row">
                            <span class="label">Bio:</span>
                            <span class="value">{{ $user->bio }}</span>
                        </div>
                    @endif
                </div>

                {{-- Settings card --}}
                <div class="card-block">
                    <h4 class="section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Settings
                    </h4>
                    <div class="settings-row">
                        <span class="label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 6 10-6"/></svg>
                            Email
                        </span>
                        <span class="value-text">{{ $user->email ?? '' }}</span>
                        <a href="{{ route('profile.edit') }}"><button class="btn-change" type="button">Change</button></a>
                    </div>
                    <div class="settings-row">
                        <span class="label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Password
                        </span>
                        <span class="value-text">••••••••••••</span>
                        <a href="{{ route('profile.edit') }}"><button class="btn-change" type="button">Change</button></a>
                    </div>
                    <div class="settings-row">
                        <span class="label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                            Language
                        </span>
                        <select class="settings-select" name="language">
                            <option @selected(($user->language ?? 'English') === 'English')>English</option>
                            <option @selected(($user->language ?? '') === 'Filipino')>Filipino</option>
                        </select>
                    </div>
                    <div class="settings-row">
                        <span class="label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            Time Zone
                        </span>
                        <select class="settings-select" name="timezone">
                            <option>{{ $user->timezone ?? '(GMT+8:00) Asia/Manila' }}</option>
                        </select>
                    </div>
                </div>

            </div>

            {{-- Right column --}}
            <div class="profile-side">

                <div class="quick-grid">
                    <a href="{{ route('courses.browse') }}" class="quick-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Courses Enrolled
                    </a>
                    <a href="{{ route('badges.index') }}" class="quick-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6 7 1-5 5 1.5 7L12 17l-6.5 4L7 14 2 9l7-1 3-6z"/></svg>
                        Badges Earned
                    </a>
                    <a href="{{ route('certificates.index') }}" class="quick-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="5"/><path d="M8 13l-2 8 6-3 6 3-2-8"/></svg>
                        Certificates
                    </a>
                    <a href="{{ route('analytics.index') }}" class="quick-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        Hours Learned
                    </a>
                </div>

                <div class="panel-card">
                    <div class="panel-card-head">
                        <h4>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10l-1 8a4 4 0 0 1-8 0L7 4z"/></svg>
                            Achievements
                        </h4>
                        <a href="{{ route('badges.index') }}">View All</a>
                    </div>
                    <div class="panel-card-body">
                        @forelse ($achievements ?? [] as $achievement)
                            <p>{{ $achievement }}</p>
                        @empty
                            No achievements to show yet.
                        @endforelse
                    </div>
                </div>

                <div class="panel-card">
                    <div class="panel-card-head">
                        <h4>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                            Recent Activities
                        </h4>
                        <a href="{{ route('analytics.index') }}">View All</a>
                    </div>
                    <div class="panel-card-body">
                        @forelse ($activities ?? [] as $activity)
                            <p>{{ $activity }}</p>
                        @empty
                            No recent activity yet.
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </main>
</div>

</body>
</html>
