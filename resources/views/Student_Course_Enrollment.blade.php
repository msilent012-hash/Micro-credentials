{{-- resources/views/Student_Course_Enrollment.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $course->title ?? 'Course' }} | Upskill</title>
<style>
    :root{
        --navy:#13176b;--navy-deep:#0c0f4d;--gold:#dba617;
        --cyan:#7fe9e3;--thumb:#d8e3f8;--ink:#13176b;
        --muted:#6b7280;--line:#e5e7eb;
        --shadow:0 10px 25px rgba(19,23,107,0.08);
        --green:#22c55e;--red:#ef4444;
    }
    *{box-sizing:border-box;}
    body{font-family:"Segoe UI",Roboto,sans-serif;color:var(--ink);margin:0;background:#f4f5f9;}
    a{text-decoration:none;color:inherit;}
    button{font-family:inherit;cursor:pointer;}

    /* ── Topbar ─────────────────────────────── */
    .topbar{background:var(--navy);display:flex;align-items:center;justify-content:space-between;padding:14px 28px;gap:20px;position:sticky;top:0;z-index:100;}
    .brand{display:flex;align-items:center;gap:14px;color:#fff;white-space:nowrap;}
    .brand .logo{width:46px;height:46px;border-radius:50%;background:#fff;flex-shrink:0;}
    .brand .logo img{width:100%;height:100%;object-fit:contain;border-radius:50%;padding:3px;}
    .brand h1{font-size:24px;letter-spacing:1px;margin:0;font-weight:800;}
    .nav-pills{display:flex;gap:14px;flex-wrap:wrap;margin-left:auto;}
    .nav-pills a{background:#fff;color:var(--navy);font-weight:700;padding:10px 26px;border-radius:999px;font-size:15px;}
    .nav-pills a.is-active{outline:2px solid var(--cyan);}
    .search-box{display:flex;align-items:center;gap:10px;background:#fff;border-radius:999px;padding:10px 18px;min-width:220px;color:var(--muted);}
    .search-box input{border:none;outline:none;font-size:15px;width:100%;color:var(--ink);background:transparent;}
    .icon-cluster{display:flex;align-items:center;gap:14px;}
    .icon-circle{width:42px;height:42px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;}
    .icon-circle svg{width:22px;height:22px;color:var(--navy);}

    /* ── Layout ─────────────────────────────── */
    .layout{display:grid;grid-template-columns:260px 1fr;height:calc(100vh - 74px);}

    /* ── Left: Course Nav ───────────────────── */
    .course-nav{background:#fff;border-right:1px solid var(--line);display:flex;flex-direction:column;overflow:hidden;}
    .course-nav-header{background:var(--navy);color:#fff;padding:20px 18px 18px;flex-shrink:0;}
    .nav-category{font-size:12px;font-weight:700;color:var(--cyan);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;}
    .nav-title{font-size:15px;font-weight:800;line-height:1.3;margin-bottom:16px;}
    .nav-progress-row{display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:6px;}
    .nav-progress-track{width:100%;height:6px;background:rgba(255,255,255,.25);border-radius:999px;overflow:hidden;}
    .nav-progress-fill{height:100%;background:var(--cyan);border-radius:999px;transition:width .5s ease;}

    .modules-scroll{flex:1;overflow-y:auto;}
    .modules-scroll::-webkit-scrollbar{width:4px;}
    .modules-scroll::-webkit-scrollbar-thumb{background:#c9cce0;border-radius:4px;}

    .module-group{border-bottom:1px solid var(--line);}
    .module-toggle{width:100%;background:#f1f2f8;border:none;padding:0;display:flex;align-items:stretch;text-align:left;}
    .module-toggle:hover{background:#e8e9f4;}
    .module-title-area{flex:1;padding:14px 16px;display:flex;flex-direction:column;gap:2px;cursor:pointer;}
    .module-num{font-size:13px;font-weight:800;color:var(--navy);}
    .module-sub{font-size:12px;font-weight:600;color:var(--muted);}
    .module-chevron-btn{background:transparent;border:none;border-left:1px solid var(--line);padding:0 14px;display:flex;align-items:center;cursor:pointer;}
    .chevron{width:18px;height:18px;color:var(--navy);transition:transform .2s;}
    .chevron.open{transform:rotate(180deg);}

    /* Module states */
    .module-title-area.active{background:var(--navy);border-left:4px solid var(--cyan);}
    .module-title-area.active .module-num,.module-title-area.active .module-sub{color:#fff;}
    .module-title-area.completed{border-left:4px solid var(--green);}
    .module-title-area.completed .module-num{color:#166534;}

    .lesson-list{display:none;}
    .lesson-list.open{display:block;}

    /* Lesson items */
    .lesson-item{display:flex;align-items:center;padding:12px 16px 12px 20px;border-bottom:1px solid #f0f0f5;cursor:pointer;transition:background .15s;gap:8px;}
    .lesson-item:hover{background:#f0f1f8;}
    .lesson-item.active{background:#e8e9f4;border-left:3px solid var(--navy);}
    .lesson-item.lesson-correct{border-left:4px solid var(--green)!important;background:#f0fdf4;}
    .lesson-item.lesson-wrong{border-left:4px solid var(--red)!important;background:#fef2f2;}
    .lesson-title-text{font-size:13px;font-weight:600;color:var(--navy);line-height:1.3;flex:1;}
    .lesson-meta{font-size:11px;color:var(--muted);white-space:nowrap;flex-shrink:0;}
    .status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;display:none;}
    .lesson-correct .status-dot{display:inline-block;background:var(--green);box-shadow:0 0 0 3px rgba(34,197,94,.2);}
    .lesson-wrong   .status-dot{display:inline-block;background:var(--red);box-shadow:0 0 0 3px rgba(239,68,68,.2);}

    /* Quiz row */
    .quiz-row{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#fef9e7;gap:10px;}
    .quiz-info-title{font-size:13px;font-weight:800;color:var(--navy);margin-bottom:2px;}
    .quiz-info-sub{font-size:11px;color:var(--muted);}
    /* LOCKED quiz button */
    .btn-quiz-sm{font-weight:800;font-size:12px;padding:7px 16px;border-radius:6px;flex-shrink:0;transition:all .2s;}
    .btn-quiz-sm.locked{background:#e5e7eb;color:#9ca3af;border:1.5px solid #e5e7eb;cursor:not-allowed;}
    .btn-quiz-sm.unlocked{background:#fff;color:var(--navy);border:1.5px solid var(--navy);cursor:pointer;}
    .btn-quiz-sm.unlocked:hover{background:var(--navy);color:#fff;}

    /* ── Right: Content Area ────────────────── */
    .lesson-content{display:flex;flex-direction:column;overflow-y:auto;padding:28px 36px 40px;gap:20px;background:#f4f5f9;}
    .lesson-header-btn{display:flex;align-items:center;justify-content:space-between;width:100%;background:var(--navy);color:#fff;font-weight:800;font-size:18px;padding:18px 28px;border:none;border-radius:14px;text-align:left;}

    /* ── VIEW 1: Welcome ────────────────────── */
    #view-welcome{display:flex;flex-direction:column;gap:20px;}
    .welcome-thumb-card{background:var(--thumb);border-radius:20px;height:220px;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow);}
    .welcome-thumb-card svg{width:56px;height:56px;color:#9aa8d4;opacity:.6;}
    .welcome-stats-card{background:#fff;border-radius:20px;box-shadow:var(--shadow);padding:28px 32px 32px;text-align:center;}
    .welcome-title{font-size:22px;font-weight:800;color:var(--navy);margin:0 0 28px;line-height:1.3;}
    .welcome-stats{display:flex;align-items:center;justify-content:center;gap:40px;}
    .w-stat .num{font-size:32px;font-weight:800;color:var(--navy);display:block;}
    .w-stat .lbl{font-size:14px;font-weight:700;color:var(--navy);margin-top:4px;}
    .btn-welcome-continue{display:flex;align-items:center;justify-content:space-between;width:100%;background:var(--navy);color:#fff;font-weight:800;font-size:18px;padding:18px 28px;border:none;border-radius:14px;cursor:pointer;}
    .btn-welcome-continue:hover{background:var(--navy-deep);}

    /* ── VIEW 2: Lesson content ─────────────── */
    #view-lesson{display:none;flex-direction:column;gap:20px;}
    .lesson-card{background:#fff;border-radius:20px;box-shadow:var(--shadow);overflow:hidden;}
    .lesson-card-topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid var(--line);}
    .lesson-card-label{font-size:16px;font-weight:800;color:var(--navy);}
    .btn-mark-complete{background:var(--navy);color:#fff;font-weight:700;font-size:14px;padding:10px 22px;border:none;border-radius:8px;transition:background .2s;}
    .btn-mark-complete:hover{background:var(--navy-deep);}
    .btn-mark-complete.done{background:var(--green);}
    /* Video player */
    .video-area{background:#1a1a3e;height:260px;display:flex;align-items:center;justify-content:center;cursor:pointer;}
    .play-btn{width:64px;height:64px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .2s;}
    .play-btn:hover{background:rgba(255,255,255,.35);}
    .play-btn svg{width:30px;height:30px;fill:#fff;margin-left:5px;}
    /* Text reading area */
    .text-area{display:none;background:var(--thumb);height:260px;flex-direction:column;align-items:center;justify-content:center;gap:14px;padding:24px;}
    .text-area svg{width:54px;height:54px;color:#7a8ec4;opacity:.7;}
    .text-area p{font-size:14px;color:var(--navy);font-weight:600;text-align:center;margin:0;opacity:.85;}
    #view-lesson.text-mode .video-area{display:none;}
    #view-lesson.text-mode .text-area{display:flex;}
    /* Lesson body */
    .lesson-body{padding:22px 26px 26px;}
    .lesson-body-title{font-size:20px;font-weight:800;color:var(--navy);margin:0 0 10px;}
    .lesson-body-desc{font-size:14px;color:var(--muted);margin:0 0 8px;line-height:1.6;}
    .lesson-body-desc strong{color:var(--navy);}
    .lesson-body-duration{font-size:13px;color:var(--muted);margin:0;}
    /* Continue = next lesson only */
    .btn-lesson-continue{display:flex;align-items:center;justify-content:center;width:100%;background:var(--navy);color:#fff;font-weight:800;font-size:18px;padding:18px 28px;border:none;border-radius:14px;cursor:pointer;}
    .btn-lesson-continue:hover{background:var(--navy-deep);}

    /* ── VIEW 3: Quiz (3 questions) ─────────── */
    #view-quiz{display:none;flex-direction:column;gap:20px;}
    .quiz-card{background:#fff;border-radius:20px;box-shadow:var(--shadow);padding:26px 28px 30px;display:flex;flex-direction:column;gap:24px;}
    .quiz-question-block{}
    .quiz-q-label{font-size:15px;font-weight:800;color:var(--navy);margin:0 0 10px;}
    .quiz-q-box{background:#f1f2f8;border-radius:10px;padding:13px 18px;font-size:14px;font-weight:600;color:var(--navy);margin-bottom:12px;}
    .quiz-options{display:flex;flex-direction:column;gap:10px;}
    .quiz-opt{display:flex;align-items:center;gap:12px;background:#f8f9fb;border:1.5px solid var(--line);border-radius:10px;padding:12px 16px;cursor:pointer;transition:all .15s;}
    .quiz-opt:hover{border-color:var(--navy);background:#eef0fa;}
    .quiz-opt.selected{border-color:var(--navy);background:#e8e9f4;}
    .quiz-opt.correct-ans{border-color:var(--green);background:#f0fdf4;}
    .quiz-opt.wrong-ans{border-color:var(--red);background:#fef2f2;}
    .quiz-opt-letter{width:28px;height:28px;border-radius:50%;background:var(--navy);color:#fff;font-weight:800;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .quiz-opt-text{font-size:14px;font-weight:600;color:var(--navy);}
    .quiz-divider{border:none;border-top:1px solid var(--line);margin:0;}
    .quiz-score-result{display:none;padding:14px 18px;border-radius:10px;font-weight:700;font-size:15px;text-align:center;}
    .quiz-score-result.pass{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;}
    .quiz-score-result.fail{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;}
    .btn-quiz-submit{display:flex;align-items:center;justify-content:center;width:100%;background:var(--navy);color:#fff;font-weight:800;font-size:18px;padding:18px 28px;border:none;border-radius:14px;cursor:pointer;}
    .btn-quiz-submit:hover{background:var(--navy-deep);}
    /* Next-module button after quiz */
    .btn-next-module{display:none;align-items:center;justify-content:center;width:100%;background:var(--green);color:#fff;font-weight:800;font-size:17px;padding:18px 28px;border:none;border-radius:14px;cursor:pointer;}
    .btn-next-module:hover{background:#16a34a;}

    /* ── Locked module styles ──────────────────── */
    .module-locked .module-title-area{opacity:.5;cursor:not-allowed;pointer-events:none;}
    .module-locked .module-chevron-btn{opacity:.5;cursor:not-allowed;pointer-events:none;}
    .module-locked .lesson-item{pointer-events:none;opacity:.4;cursor:not-allowed;}
    .module-locked .btn-quiz-sm{pointer-events:none;opacity:.4;}
    .module-lock-badge{font-size:11px;color:var(--muted);font-weight:700;
        display:flex;align-items:center;gap:4px;margin-top:3px;}
    /* Retake quiz button */
    .btn-retake{display:none;width:100%;padding:12px 20px;border:2px solid var(--navy);
        background:transparent;color:var(--navy);font-weight:800;font-size:15px;
        border-radius:14px;cursor:pointer;margin-top:0;}
    .btn-retake:hover{background:var(--navy);color:#fff;}

    @media(max-width:980px){
        .layout{grid-template-columns:1fr;height:auto;}
        .course-nav{max-height:340px;}
        .lesson-content{padding:20px 18px 40px;}
    }
</style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <span class="logo"><img src="{{ asset('images/PSU-Logo.png') }}" alt="PSU Logo"></span>
        <h1>UPSKILL</h1>
    </div>
    <nav class="nav-pills">
        <a href="{{ route('courses.browse') }}" class="{{ request()->routeIs('courses.*') ? 'is-active' : '' }}">Courses</a>
        <a href="{{ route('dashboard') }}"      class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
    </nav>
    <form action="{{ route('search') }}" method="GET" class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" name="q" placeholder="Search" value="{{ request('q') }}">
    </form>
    <div class="icon-cluster">
        <a href="{{ route('notifications.index') }}" class="icon-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a href="{{ route('profile.show') }}" class="icon-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/></svg>
        </a>
    </div>
</header>

<div class="layout">

    {{-- ── LEFT: Course Navigation ─── --}}
    <aside class="course-nav">
        <div class="course-nav-header">
            @if($course->category ?? false)<div class="nav-category">{{ $course->category }}</div>@endif
            <div class="nav-title">{{ $course->title ?? 'Course' }}</div>
            <div class="nav-progress-row">
                <span>Your Progress</span>
                <span id="nav-pct">0%</span>
            </div>
            <div class="nav-progress-track">
                <div class="nav-progress-fill" id="nav-fill" style="width:0%"></div>
            </div>
        </div>

        <div class="modules-scroll">
            @foreach($modules as $mIndex => $module)
            <div class="module-group {{ $mIndex > 0 ? 'module-locked' : '' }}" id="mg-{{ $mIndex }}">

                <div class="module-toggle">
                    <div class="module-title-area" id="mta-{{ $mIndex }}"
                         onclick="showModuleWelcome('{{ addslashes($module->title) }}',{{ $module->lessons->count() }},{{ $modules->count() }},{{ $badge_count ?? 1 }},{{ $mIndex }})">
                        <span class="module-num">Module {{ $mIndex + 1 }}: {{ $module->title }}</span>
                        <span class="module-sub" id="msub-{{ $mIndex }}">{{ $module->lessons->count() }} lessons</span>
                        @if($mIndex > 0)
                        <span class="module-lock-badge" id="mlock-{{ $mIndex }}">
                            Complete previous quiz to unlock
                        </span>
                        @endif
                    </div>
                    <button class="module-chevron-btn" type="button"
                            onclick="toggleModule('mod-{{ $mIndex }}','chev-{{ $mIndex }}')">
                        <svg class="chevron {{ $mIndex===0?'open':'' }}" id="chev-{{ $mIndex }}"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                <div class="lesson-list {{ $mIndex===0?'open':'' }}" id="mod-{{ $mIndex }}">
                    @foreach($module->lessons ?? [] as $lIndex => $lesson)
                        @php $isVid = strtolower($lesson->type??'')  === 'video'; @endphp
                        <div class="lesson-item"
                             id="li-{{ $mIndex }}-{{ $lIndex }}"
                             data-module="{{ $mIndex }}"
                             data-lid="{{ $mIndex }}-{{ $lIndex }}"
                             onclick="{{ $isVid ? 'loadVideoLesson' : 'loadTextLesson' }}(
                                 '{{ addslashes($lesson->title) }}',
                                 '{{ addslashes($lesson->description ?? $lesson->title) }}',
                                 '{{ $lesson->duration ?? '15m' }}',
                                 {{ $mIndex }}, {{ $lIndex }}
                             )">
                            <span class="lesson-title-text">{{ $lesson->title }}</span>
                            <span class="lesson-meta">{{ $lesson->type }} · {{ $lesson->duration }}</span>
                            <span class="status-dot"></span>
                        </div>
                    @endforeach

                    @if($module->quiz ?? false)
                    <div class="quiz-row">
                        <div>
                            <div class="quiz-info-title">{{ $module->quiz->title }}</div>
                            <div class="quiz-info-sub">{{ $module->quiz->questions_count ?? 3 }} questions · Pass {{ $module->quiz->passing_score ?? 75 }}%</div>
                        </div>
                        {{-- Starts LOCKED; JS unlocks after all lessons marked complete --}}
                        <button class="btn-quiz-sm locked" id="qbtn-{{ $mIndex }}"
                                data-module="{{ $mIndex }}"
                                onclick="handleQuizClick({{ $mIndex }})"
                                title="Complete all lessons first">QUIZ</button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </aside>

    {{-- ── RIGHT: Content ─── --}}
    <main class="lesson-content">

        {{-- VIEW 1: Welcome --}}
        <div id="view-welcome">
            <div class="lesson-header-btn">
                <span id="wh-title">{{ $modules->first()->title ?? 'Module' }}</span>
                <span>&#x2192;</span>
            </div>
            <div class="welcome-thumb-card">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <div class="welcome-stats-card">
                <h2 class="welcome-title" id="wh-mod-title">Welcome to {{ $modules->first()->title ?? 'the Course' }}</h2>
                <div class="welcome-stats">
                    <div class="w-stat"><span class="num" id="ws-mod">{{ $modules->count() }}</span><div class="lbl">Modules</div></div>
                    <div class="w-stat"><span class="num" id="ws-les">{{ $total_lessons ?? 0 }}</span><div class="lbl">Lessons</div></div>
                    <div class="w-stat"><span class="num" id="ws-bdg">{{ $badge_count ?? 1 }}</span><div class="lbl">Badge</div></div>
                </div>
            </div>
            <button class="btn-welcome-continue" onclick="continueFromWelcome()" type="button">
                <span>Continue</span><span>&#x2192;</span>
            </button>
        </div>

        {{-- VIEW 2: Lesson Content --}}
        <div id="view-lesson">
            <div class="lesson-header-btn">
                <span id="lh-title">Introduction to Laravel</span>
                <span>&#x2192;</span>
            </div>
            <div class="lesson-card">
                <div class="lesson-card-topbar">
                    <span class="lesson-card-label" id="lc-label">Introduction to Laravel</span>
                    <button class="btn-mark-complete" id="btn-mark" onclick="markComplete()" type="button">Mark Complete</button>
                </div>
                {{-- Video --}}
                <div class="video-area" onclick="playVideo()">
                    <div class="play-btn" id="play-btn">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polygon points="5,3 19,12 5,21"/></svg>
                    </div>
                </div>
                {{-- Text --}}
                <div class="text-area" id="text-area">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <p id="text-snippet">Read the lesson content below</p>
                </div>
                <div class="lesson-body">
                    <h3 class="lesson-body-title" id="lb-title">What is Introduction to Laravel?</h3>
                    <p class="lesson-body-desc"  id="lb-desc"><strong>Introduction to Laravel</strong> &mdash; lesson content</p>
                    <p class="lesson-body-duration" id="lb-dur">Duration: 15m</p>
                </div>
            </div>
            {{-- Continue = next lesson only (no quiz) --}}
            <button class="btn-lesson-continue" onclick="nextLesson()" type="button">Continue</button>
        </div>

        {{-- VIEW 3: Quiz (3 questions) --}}
        <div id="view-quiz">
            <div class="lesson-header-btn">
                <span id="qh-title">Quiz</span>
                <span>&#x2192;</span>
            </div>
            <div class="quiz-card" id="quiz-questions-container">
                {{-- Populated by JS --}}
            </div>
            <div class="quiz-score-result" id="quiz-score-result"></div>
            <button class="btn-quiz-submit" id="btn-quiz-submit" onclick="submitQuiz()" type="button">Submit</button>
            <button class="btn-next-module" id="btn-next-module" onclick="goToNextModule()" type="button">
                Continue to Next Module &#x2192;
            </button>
            <button class="btn-retake" id="btn-retake" onclick="retakeQuiz()" type="button">
                &#x21BA; Retake Quiz
            </button>
        </div>

    </main>
</div>

<script>
/* ═══════════════════════════════════════════════════════
   QUIZ DATA — one per module, 3 questions each
═══════════════════════════════════════════════════════ */
var QUIZ_DATA = {
    0: {   /* Module 1: Laravel Fundamentals */
        title: 'Laravel Fundamentals Quiz',
        passingScore: 75,
        questions: [
            {
                label: 'Introduction to Laravel',
                question: 'What is Laravel?',
                options: [
                    { letter:'A', text:'MVC Framework' },
                    { letter:'B', text:'PHP Language'  },
                    { letter:'C', text:'Database Tool' }
                ],
                correct: 'A'
            },
            {
                label: 'Routing and Controllers',
                question: 'What is Routing and Controllers?',
                options: [
                    { letter:'A', text:'URL Mapping & Logic Handlers' },
                    { letter:'B', text:'Database ORM'  },
                    { letter:'C', text:'View Engine'   }
                ],
                correct: 'A'
            },
            {
                label: 'Blade Templates',
                question: 'What is Blade Templates?',
                options: [
                    { letter:'A', text:'CSS Framework'      },
                    { letter:'B', text:'PHP Template Engine' },
                    { letter:'C', text:'JavaScript Library' }
                ],
                correct: 'B'
            }
        ]
    },
    1: {   /* Module 2: Database & Eloquent ORM */
        title: 'Database & Eloquent ORM Quiz',
        passingScore: 75,
        questions: [
            {
                label: 'Database Migrations',
                question: 'What is Database Migrations?',
                options: [
                    { letter:'A', text:'Schema Version Control' },
                    { letter:'B', text:'Data Transfer Protocol' },
                    { letter:'C', text:'Table Relationship'     }
                ],
                correct: 'A'
            },
            {
                label: 'Eloquent Relationships',
                question: 'What is Eloquent Relationships?',
                options: [
                    { letter:'A', text:'SQL Query Builder'   },
                    { letter:'B', text:'ORM Model Relations' },
                    { letter:'C', text:'Data Migration'      }
                ],
                correct: 'B'
            },
            {
                label: 'Eloquent ORM',
                question: 'What is Eloquent ORM?',
                options: [
                    { letter:'A', text:'Database Query Tool'      },
                    { letter:'B', text:'Object-Relational Mapper' },
                    { letter:'C', text:'Migration Manager'        }
                ],
                correct: 'B'
            }
        ]
    }
};

/* ═══════════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════════ */
var _curLessonEl   = null;
var _curModIdx     = 0;
var _curLesIdx     = 0;
var _totalQuizQ    = 0;          // total quiz questions across all modules
var _moduleScores  = {};         // { modIdx: correctCount } — replaced on retake

// Count total quiz questions on load
document.addEventListener('DOMContentLoaded', function () {
    Object.values(QUIZ_DATA).forEach(function(m){ _totalQuizQ += m.questions.length; });
});

/* ── Helpers ──────────────────────────────────────── */
function hideAllViews() {
    document.getElementById('view-welcome').style.display = 'none';
    document.getElementById('view-lesson').style.display  = 'none';
    document.getElementById('view-quiz').style.display    = 'none';
}
function toggleModule(listId, chevId) {
    const l = document.getElementById(listId), c = document.getElementById(chevId);
    const o = l.classList.toggle('open'); c.classList.toggle('open', o);
}

/* ── Progress bar — based on quiz correct answers ONLY ── */
function updateProgress() {
    /* Sum correct answers per module (retakes REPLACE, not add).
       Only correct answers count — wrong answers contribute 0. */
    const totalCorrect = Object.values(_moduleScores).reduce(function(sum, v){ return sum + v; }, 0);
    const raw = _totalQuizQ > 0 ? Math.round((totalCorrect / _totalQuizQ) * 100) : 0;
    const pct = Math.min(100, raw);
    document.getElementById('nav-fill').style.width = pct + '%';
    document.getElementById('nav-pct').textContent  = pct + '%';
}

/* ── Module Welcome ───────────────────────────────── */
function showModuleWelcome(title, lessonCount, modCount, badgeCount, modIdx) {
    document.querySelectorAll('.module-title-area').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
    const ta = document.getElementById('mta-' + modIdx);
    if (ta && !ta.classList.contains('completed')) ta.classList.add('active');
    _curModIdx = modIdx;

    document.getElementById('wh-title').textContent     = title;
    document.getElementById('wh-mod-title').textContent = 'Welcome to ' + title;
    document.getElementById('ws-mod').textContent       = modCount;
    document.getElementById('ws-les').textContent       = lessonCount;
    document.getElementById('ws-bdg').textContent       = badgeCount;

    hideAllViews();
    document.getElementById('view-welcome').style.display = 'flex';
}
function continueFromWelcome() {
    const first = document.querySelector('#mg-' + _curModIdx + ' .lesson-item');
    if (first) first.click();
}

/* ── Load lesson helper ───────────────────────────── */
function _prepLesson(title, desc, duration, modIdx, lesIdx) {
    document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.module-title-area').forEach(el => el.classList.remove('active'));
    const el = document.getElementById('li-' + modIdx + '-' + lesIdx);
    if (el) el.classList.add('active');
    _curLessonEl = el;
    _curModIdx   = modIdx;
    _curLesIdx   = lesIdx;

    document.getElementById('lh-title').textContent   = title;
    document.getElementById('lc-label').textContent   = title;
    document.getElementById('lb-title').textContent   = 'What is ' + title + '?';
    document.getElementById('lb-desc').innerHTML      = '<strong>' + title + '</strong> \u2014 ' + (desc || title);
    document.getElementById('lb-dur').textContent     = 'Duration: ' + (duration || '15m');
    document.getElementById('text-snippet').textContent = 'Read: ' + (desc || title);

    const btn = document.getElementById('btn-mark');
    // Restore green if already marked
    if (_curLessonEl && _curLessonEl.classList.contains('lesson-correct')) {
        btn.textContent = '\u2713 Completed';
        btn.classList.add('done');
    } else {
        btn.textContent = 'Mark Complete';
        btn.classList.remove('done');
    }
    document.getElementById('play-btn').style.display = 'flex';
}

function loadVideoLesson(title, desc, duration, modIdx, lesIdx) {
    _prepLesson(title, desc, duration, modIdx, lesIdx);
    document.getElementById('view-lesson').classList.remove('text-mode');
    hideAllViews();
    document.getElementById('view-lesson').style.display = 'flex';
}
function loadTextLesson(title, desc, duration, modIdx, lesIdx) {
    _prepLesson(title, desc, duration, modIdx, lesIdx);
    document.getElementById('view-lesson').classList.add('text-mode');
    hideAllViews();
    document.getElementById('view-lesson').style.display = 'flex';
}

/* ── Mark Complete → green dot + unlock quiz check ── */
function markComplete() {
    const btn  = document.getElementById('btn-mark');
    const done = btn.classList.toggle('done');
    btn.textContent = done ? '\u2713 Completed' : 'Mark Complete';

    if (_curLessonEl) {
        if (done) {
            _curLessonEl.classList.add('lesson-correct');
            _curLessonEl.classList.remove('lesson-wrong', 'active');
        } else {
            _curLessonEl.classList.remove('lesson-correct');
        }
    }
    checkQuizUnlock(_curModIdx);
    checkModuleComplete(_curModIdx);
}

/* ── Unlock QUIZ when all lessons are marked complete ─ */
function checkQuizUnlock(modIdx) {
    const group   = document.getElementById('mg-' + modIdx);
    const lessons = Array.from(group.querySelectorAll('.lesson-item'));
    const allDone = lessons.length > 0 && lessons.every(l => l.classList.contains('lesson-correct'));
    const qbtn    = document.getElementById('qbtn-' + modIdx);
    if (!qbtn) return;
    if (allDone) {
        qbtn.classList.replace('locked', 'unlocked');
        qbtn.title = 'Take the quiz!';
    } else {
        qbtn.classList.replace('unlocked', 'locked');
        qbtn.title = 'Complete all lessons first';
    }
}

/* ── Module completion marker ─────────────────────── */
function checkModuleComplete(modIdx) {
    const group   = document.getElementById('mg-' + modIdx);
    const lessons = Array.from(group.querySelectorAll('.lesson-item'));
    const allDone = lessons.every(l => l.classList.contains('lesson-correct'));
    const ta      = document.getElementById('mta-' + modIdx);
    const sub     = document.getElementById('msub-' + modIdx);
    if (allDone && lessons.length > 0) {
        ta.classList.add('completed');
        ta.classList.remove('active');
        sub.innerHTML = '<span style="color:var(--green);font-weight:800;">\u2713 Completed</span>';
    }
}

/* ── Next lesson (Continue button) ───────────────── */
function nextLesson() {
    const all    = Array.from(document.querySelectorAll('.lesson-item'));
    const idx    = all.indexOf(_curLessonEl);
    const next   = all[idx + 1];

    // If next lesson exists AND is in the same module → go to it
    if (next && parseInt(next.dataset.module) === _curModIdx) {
        next.click();
        return;
    }

    // Otherwise (last lesson of this module) → return to module welcome screen
    const ta       = document.getElementById('mta-' + _curModIdx);
    const modTitle = ta ? ta.querySelector('.module-num').textContent.replace(/^Module \d+:\s*/,'') : 'the Module';
    const group    = document.getElementById('mg-' + _curModIdx);
    const lesCount = group ? group.querySelectorAll('.lesson-item').length : 0;

    showModuleWelcome(
        modTitle,
        lesCount,
        document.querySelectorAll('.module-group').length,
        {{ $badge_count ?? 1 }},
        _curModIdx
    );
}

/* ── QUIZ button click ────────────────────────────── */
function handleQuizClick(modIdx) {
    const qbtn = document.getElementById('qbtn-' + modIdx);
    if (qbtn && qbtn.classList.contains('locked')) {
        alert('Please mark all lessons as complete before taking the quiz.');
        return;
    }
    showQuizView(modIdx);
}

/* ── Build quiz view (3 questions) ───────────────── */
function showQuizView(modIdx) {
    _curModIdx = modIdx;
    const data = QUIZ_DATA[modIdx];
    if (!data) return;

    document.getElementById('qh-title').textContent = data.title;

    const container = document.getElementById('quiz-questions-container');
    container.innerHTML = '';

    data.questions.forEach(function(q, qi) {
        // Divider between questions
        if (qi > 0) {
            const hr = document.createElement('hr');
            hr.className = 'quiz-divider';
            container.appendChild(hr);
        }

        const block = document.createElement('div');
        block.className = 'quiz-question-block';
        block.id = 'qblock-' + qi;

        const label = document.createElement('div');
        label.className = 'quiz-q-label';
        label.textContent = q.label;
        block.appendChild(label);

        const qbox = document.createElement('div');
        qbox.className = 'quiz-q-box';
        qbox.textContent = q.question;
        block.appendChild(qbox);

        const optWrap = document.createElement('div');
        optWrap.className = 'quiz-options';
        optWrap.id = 'opts-' + qi;

        q.options.forEach(function(opt) {
            const div = document.createElement('div');
            div.className = 'quiz-opt';
            div.dataset.letter = opt.letter;
            div.innerHTML = '<span class="quiz-opt-letter">' + opt.letter + '</span>'
                          + '<span class="quiz-opt-text">'   + opt.text   + '</span>';
            div.onclick = function() {
                optWrap.querySelectorAll('.quiz-opt').forEach(el => el.classList.remove('selected'));
                div.classList.add('selected');
            };
            optWrap.appendChild(div);
        });
        block.appendChild(optWrap);
        container.appendChild(block);
    });

    // Reset result & buttons
    document.getElementById('quiz-score-result').style.display  = 'none';
    document.getElementById('btn-quiz-submit').style.display    = 'flex';
    document.getElementById('btn-next-module').style.display    = 'none';

    hideAllViews();
    document.getElementById('view-quiz').style.display = 'flex';
}

/* ── Submit quiz ──────────────────────────────────── */
function submitQuiz() {
    const data = QUIZ_DATA[_curModIdx];
    if (!data) return;

    let answered = true, correctCount = 0;

    data.questions.forEach(function(q, qi) {
        const selected = document.querySelector('#opts-' + qi + ' .quiz-opt.selected');
        if (!selected) { answered = false; return; }

        const letter    = selected.dataset.letter;
        const isCorrect = letter === q.correct;
        if (isCorrect) correctCount++;

        // Highlight options
        document.querySelectorAll('#opts-' + qi + ' .quiz-opt').forEach(function(opt) {
            opt.classList.remove('selected');
            if (opt.dataset.letter === q.correct) opt.classList.add('correct-ans');
            else if (opt.dataset.letter === letter && !isCorrect) opt.classList.add('wrong-ans');
        });
    });

    if (!answered) {
        alert('Please answer all questions before submitting.');
        return;
    }

    // Store correct count for this module (replaces previous attempt — retakes don't stack)
    _moduleScores[_curModIdx] = correctCount;
    updateProgress();

    // Score result
    const pct      = Math.round((correctCount / data.questions.length) * 100);
    const pass     = pct >= (data.passingScore || 75);
    const scoreEl  = document.getElementById('quiz-score-result');
    scoreEl.innerHTML  = pass
        ? 'You passed! Score: ' + correctCount + '/' + data.questions.length
        : 'Score: ' + correctCount + '/' + data.questions.length + ' \u2014 Keep practicing!';
    scoreEl.className  = 'quiz-score-result ' + (pass ? 'pass' : 'fail');
    scoreEl.style.display = 'block';

    // Hide submit button
    document.getElementById('btn-quiz-submit').style.display = 'none';

    const moduleCount = Object.keys(QUIZ_DATA).length;
    const hasNextMod  = _curModIdx < moduleCount - 1;

    if (pass) {
        // ✅ PASSED → unlock next module
        if (hasNextMod) {
            unlockModule(_curModIdx + 1);
            const nb = document.getElementById('btn-next-module');
            nb.textContent   = 'Continue to Module ' + (_curModIdx + 2) + ' →';
            nb.style.display = 'flex';
        }
    } else {
        // ❌ FAILED → next module stays locked, offer retake
        document.getElementById('btn-retake').style.display = 'block';
    }
}

/* ── Unlock a module (remove locked state) ───────── */
function unlockModule(modIdx) {
    const group = document.getElementById('mg-' + modIdx);
    if (!group) return;
    group.classList.remove('module-locked');
    // Hide lock badge
    const lockBadge = document.getElementById('mlock-' + modIdx);
    if (lockBadge) lockBadge.style.display = 'none';
    // Update module sub text
    const sub = document.getElementById('msub-' + modIdx);
    if (sub) sub.innerHTML = '<span style="color:var(--green);font-size:11px;font-weight:700;">✓ Unlocked</span>';
}

/* ── Retake quiz ──────────────────────────────────── */
function retakeQuiz() {
    document.getElementById('btn-retake').style.display       = 'none';
    document.getElementById('quiz-score-result').style.display = 'none';
    showQuizView(_curModIdx);   // next submit will overwrite _moduleScores[_curModIdx]
}

/* ── Go to next module ────────────────────────────── */
function goToNextModule() {
    const nextIdx = _curModIdx + 1;
    const list    = document.getElementById('mod-' + nextIdx);
    const chev    = document.getElementById('chev-' + nextIdx);
    if (list) { list.classList.add('open'); if (chev) chev.classList.add('open'); }
    const first   = document.querySelector('#mg-' + nextIdx + ' .lesson-item');
    if (first) first.click();
}

function playVideo() {
    const pb = document.getElementById('play-btn');
    pb.style.display = pb.style.display === 'none' ? 'flex' : 'none';
}
</script>
</body>
</html>