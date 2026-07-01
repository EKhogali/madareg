<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');

:root {
    --road-bg:    #2D2D2D;
    --gold:       #F4A623;
    --blue:       #0076BF;
    --navy:       #1A2B4A;
    --cyan:       #00BCD4;
    --stage-1:    #3B82F6;
    --stage-2:    #8B5CF6;
    --stage-3:    #F59E0B;
    --stage-4:    #EF4444;
    --stage-5:    #10B981;
}

*, *::before, *::after { box-sizing: border-box; }

.madmar-page {
    font-family: 'Tajawal', sans-serif;
    direction: rtl;
    background: linear-gradient(160deg, #EEF9FF 0%, #F0F4FF 100%);
    min-height: 100vh;
    padding: 0 0 4rem;
}

/* ── Hero ── */
.madmar-hero {
    background: linear-gradient(135deg, #0A1628 0%, #0076BF 55%, #00A8E8 100%);
    border-radius: 1.5rem;
    padding: 1.75rem 2rem;
    margin-bottom: 1.75rem;
    display: flex; align-items: center; gap: 1.5rem;
    box-shadow: 0 8px 32px rgba(0,118,191,0.25);
    position: relative; overflow: hidden;
}
.madmar-hero::after {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(244,166,35,0.15), transparent 70%);
    pointer-events: none;
}
.hero-icon { font-size: 3rem; z-index:1; animation: road-bounce 2s ease-in-out infinite; }
@keyframes road-bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
.hero-info { flex:1; z-index:1; }
.hero-info h1 { color:var(--gold); font-size:1.7rem; font-weight:900; margin:0 0 0.2rem; }
.hero-info p  { color:rgba(255,255,255,0.7); font-size:0.9rem; margin:0; }
.hero-stat {
    z-index:1; background:rgba(255,255,255,0.1);
    border:1px solid rgba(255,255,255,0.2); border-radius:1rem;
    padding:0.75rem 1.25rem; text-align:center;
    backdrop-filter:blur(8px); flex-shrink:0;
}
.hero-stat strong { display:block; color:var(--gold); font-size:1.9rem; font-weight:900; line-height:1; }
.hero-stat span   { color:rgba(255,255,255,0.65); font-size:0.72rem; }

/* ══════════════════════════════
   STAGE ZOOM BUTTONS
══════════════════════════════ */
.stage-zoom-bar {
    display:flex; gap:0.5rem; flex-wrap:wrap;
    margin-bottom:1.25rem; align-items:center;
}
.stage-zoom-btn {
    display:flex; align-items:center; gap:0.35rem;
    padding:0.38rem 1.05rem;
    border-radius:2rem; font-size:0.82rem; font-weight:700;
    color:white; border:2.5px solid rgba(255,255,255,0.3);
    cursor:pointer;
    box-shadow:0 2px 8px rgba(0,0,0,0.12);
    transition:transform 0.22s ease, box-shadow 0.22s ease,
               border-color 0.22s ease, opacity 0.22s ease;
    user-select:none; font-family:inherit;
}
.stage-zoom-btn:hover {
    transform:translateY(-2px);
    box-shadow:0 5px 16px rgba(0,0,0,0.2);
    border-color:white;
}
.stage-zoom-btn.active {
    border-color:white;
    box-shadow:0 0 0 3px rgba(255,255,255,0.5), 0 6px 24px rgba(0,0,0,0.25);
    transform:translateY(-2px) scale(1.07);
}
.stage-reset-btn {
    display:none; align-items:center; gap:0.35rem;
    padding:0.38rem 1.05rem;
    border-radius:2rem; font-size:0.82rem; font-weight:700;
    color:#475569; background:white;
    border:2px solid #E2E8F0;
    cursor:pointer;
    box-shadow:0 2px 8px rgba(0,0,0,0.06);
    transition:all 0.2s ease; font-family:inherit;
}
.stage-reset-btn:hover { border-color:#94A3B8; transform:translateY(-1px); }
.stage-reset-btn.visible { display:flex; }

/* ── Filter ── */
.filter-bar {
    background:white; border-radius:1rem;
    padding:0.9rem 1.25rem; margin-bottom:1.5rem;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
}
.filter-bar label { font-weight:700; color:var(--navy); font-size:0.88rem; white-space:nowrap; }
.filter-bar select {
    flex:1; min-width:160px;
    border:1.5px solid #E2E8F0; border-radius:0.5rem;
    padding:0.4rem 0.7rem; font-size:0.88rem; color:var(--navy);
    background:#F8FAFC; outline:none; font-family:inherit; direction:rtl;
}
.filter-bar select:focus { border-color:var(--blue); }

/* ── Track scroll + zoom wrapper ── */
.track-scroll { overflow-x:auto; padding-bottom:1rem; }

.track-zoom-wrapper {
    transition: transform 0.55s cubic-bezier(0.4,0,0.2,1);
    transform-origin: center top;
    will-change: transform;
}

/* ── Road segment ── */
.road-segment {
    position:absolute;
    background:var(--road-bg);
    border-radius:28px;
    box-shadow:0 4px 18px rgba(0,0,0,0.22);
    transition: opacity 0.4s ease;
}
.road-segment::after {
    content:'';
    position:absolute;
    top:50%; left:16px; right:16px; height:3px;
    transform:translateY(-50%);
    background:repeating-linear-gradient(
        90deg,
        rgba(255,255,255,0.55) 0px, rgba(255,255,255,0.55) 18px,
        transparent 18px, transparent 32px
    );
    border-radius:2px;
}
.road-segment.vertical::after {
    top:16px; bottom:16px; left:50%; right:auto;
    width:3px; height:auto; transform:translateX(-50%);
    background:repeating-linear-gradient(
        180deg,
        rgba(255,255,255,0.55) 0px, rgba(255,255,255,0.55) 18px,
        transparent 18px, transparent 32px
    );
}

/* ── Stage color overlays ── */
.road-overlay {
    position:absolute; border-radius:28px;
    opacity:0.18; pointer-events:none; z-index:1;
    transition: opacity 0.4s ease;
}

/* ── Spotlight glow border around active stage area ── */
.stage-spotlight {
    position:absolute; border-radius:32px;
    pointer-events:none; z-index:3;
    opacity:0; border:3px solid transparent;
    transition: opacity 0.4s ease, box-shadow 0.4s ease;
}
.stage-spotlight.lit {
    opacity:1;
}

/* ── Dimming non-active milestones/clusters ── */
.milestone-pin, .sub-cluster {
    transition: opacity 0.4s ease, filter 0.4s ease;
}
.track-zoom-wrapper.has-focus .milestone-pin.dimmed,
.track-zoom-wrapper.has-focus .sub-cluster.dimmed {
    opacity: 0.15;
    filter: grayscale(90%) blur(0.5px);
}
.track-zoom-wrapper.has-focus .milestone-pin.lit,
.track-zoom-wrapper.has-focus .sub-cluster.lit {
    opacity: 1; filter: none;
}
.track-zoom-wrapper.has-focus .road-segment.dimmed,
.track-zoom-wrapper.has-focus .road-overlay.dimmed {
    opacity: 0.25;
}
.track-zoom-wrapper.has-focus .road-segment.lit { opacity:1; }
.track-zoom-wrapper.has-focus .road-overlay.lit { opacity:0.35; }

/* ── Milestone pin ── */
.milestone-pin {
    position:absolute; display:flex; flex-direction:column; align-items:center;
    z-index:10; transform:translate(-50%,-50%);
}
.milestone-dot {
    width:28px; height:28px; background:var(--gold);
    border:3px solid white; border-radius:50%;
    box-shadow:0 2px 10px rgba(0,0,0,0.25);
    display:flex; align-items:center; justify-content:center;
    font-size:0.7rem; color:white; font-weight:900; z-index:2;
}
.milestone-label {
    background:var(--cyan); color:white; font-size:0.72rem; font-weight:800;
    padding:0.15rem 0.55rem; border-radius:0.4rem;
    margin-top:3px; white-space:nowrap; box-shadow:0 1px 6px rgba(0,0,0,0.18);
}
.milestone-title {
    background:white; color:var(--navy); font-size:0.65rem; font-weight:700;
    padding:0.1rem 0.4rem; border-radius:0.3rem;
    margin-top:2px; border:1px solid #E2E8F0; white-space:nowrap;
}

/* ── Start / Finish ── */
.start-marker,.finish-marker {
    position:absolute; border-radius:0.5rem;
    padding:0.3rem 0.85rem; font-size:0.85rem; font-weight:900;
    z-index:12; transform:translate(-50%,-50%);
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
    transition: opacity 0.4s ease;
}
.start-marker  { background:#EF4444; color:white; }
.finish-marker { background:var(--gold); color:white; font-size:1rem; }
.finish-trophy { font-size:2rem; position:absolute; z-index:13; transform:translate(-50%,-50%); transition: opacity 0.4s ease; }

/* ══════════════════════════════
   SUBSCRIBER CLUSTER + HOVER CARD
══════════════════════════════ */
.sub-cluster {
    position:absolute; display:flex; flex-wrap:wrap; gap:3px;
    z-index:20; transform:translate(-50%,-50%);
    max-width:90px; justify-content:center;
}
.sub-avatar {
    position:relative; width:34px; height:34px;
    border-radius:50%;
    cursor:pointer; flex-shrink:0;
    transition:transform 0.2s ease;
    /* overflow:visible so hover-card is not clipped */
    overflow:visible;
}
.sub-avatar:hover { transform:scale(1.3); z-index:100; }

/* Inner circle: clips the image, shows initials behind */
.sub-avatar-inner {
    position:absolute; top:0; left:0;
    width:100%; height:100%;
    border-radius:50%;
    border:2.5px solid white;
    box-shadow:0 2px 6px rgba(0,0,0,0.22);
    display:flex; align-items:center; justify-content:center;
    font-size:0.75rem; font-weight:800; color:white;
    overflow:hidden; /* clips the image inside the circle */
    background:inherit;
}
.sub-avatar-inner img {
    width:100%; height:100%;
    object-fit:cover; border-radius:50%;
    display:block;
}
.sub-count-badge {
    width:34px; height:34px; border-radius:50%;
    background:var(--navy); color:white; border:2px solid white;
    display:flex; align-items:center; justify-content:center;
    font-size:0.65rem; font-weight:800; box-shadow:0 2px 6px rgba(0,0,0,0.22);
    flex-shrink:0;
}

/* ── Hover card ── */
.hover-card {
    display:none; position:absolute;
    bottom:calc(100% + 10px); left:50%; transform:translateX(-50%);
    width:180px; background:white; border-radius:0.85rem;
    box-shadow:0 8px 28px rgba(0,0,0,0.18);
    padding:0.75rem; z-index:200; pointer-events:none;
    border:1.5px solid #F1F5F9;
    animation:card-pop 0.15s ease;
}
@keyframes card-pop {
    from { opacity:0; transform:translateX(-50%) scale(0.9); }
    to   { opacity:1; transform:translateX(-50%) scale(1); }
}
.sub-avatar:hover .hover-card { display:block; }
.hover-card-top {
    display:flex; align-items:center; gap:0.5rem;
    margin-bottom:0.5rem; padding-bottom:0.5rem;
    border-bottom:1px solid #F1F5F9;
}
.hover-avatar {
    width:36px; height:36px; border-radius:50%;
    object-fit:cover; flex-shrink:0;
    border:2px solid #E2E8F0;
    display:flex; align-items:center; justify-content:center;
    font-size:0.9rem; font-weight:800; color:white; overflow:hidden;
}
.hover-avatar img { width:100%; height:100%; object-fit:cover; }
.hover-name { font-size:0.78rem; font-weight:800; color:var(--navy); line-height:1.2; }
.hover-group { font-size:0.65rem; color:#94A3B8; }
.hover-row { display:flex; justify-content:space-between; font-size:0.68rem; margin-bottom:0.2rem; }
.hover-row .lbl { color:#94A3B8; }
.hover-row .val { color:var(--navy); font-weight:700; }
.hover-pts-bar { margin-top:0.45rem; background:#F1F5F9; border-radius:999px; height:5px; overflow:hidden; }
.hover-pts-fill { height:100%; border-radius:999px; }

/* ── Legend ── */
.legend-bar {
    background:white; border-radius:1rem;
    padding:1rem 1.5rem; margin-top:1.5rem;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    display:flex; flex-wrap:wrap; gap:0.75rem 1.5rem; align-items:center;
}
.legend-bar h4 { font-weight:800; color:var(--navy); font-size:0.9rem; margin:0 0 0.5rem; width:100%; }
.legend-item { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#475569; }
.legend-dot  { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
.legend-cnt  { background:#E0F2FE; color:#0369A1; border-radius:999px; padding:0 6px; font-size:0.7rem; font-weight:800; }

/* ── Empty ── */
.empty-track { text-align:center; padding:3rem; color:#94A3B8; }
.empty-track .e-icon { font-size:3rem; margin-bottom:0.75rem; }

@media(max-width:700px) {
    .madmar-hero { flex-direction:column; text-align:center; }
    .track-scroll { padding:0 0.5rem; }
}
</style>

@php
    $u         = auth()->user();
    $role      = (int) $u?->role;
    $groupOpts = $this->groupOptions();
    $subs      = $this->subscribers;
    $total     = count($subs);

    $byMilestone = collect($subs)->groupBy('milestone')->toArray();

    $milestoneStageColor = [
        1 => '#3B82F6', 2 => '#3B82F6',
        3 => '#8B5CF6', 4 => '#8B5CF6',
        5 => '#F59E0B', 6 => '#F59E0B',
        7 => '#EF4444', 8 => '#EF4444',
        9 => '#10B981', 10 => '#10B981',
    ];

    // milestone id → stage id (1-5)
    $milestoneStageId = [
        1=>1,2=>1, 3=>2,4=>2, 5=>3,6=>3, 7=>4,8=>4, 9=>5,10=>5
    ];

    $rh = 56; $rw = 56;
    $y4 = 100; $y3 = 260; $y2 = 420; $y1 = 580;
    $xL = 60;  $xR = 620;

    $mPos = [
        1  => ['x' => 140,      'y' => $y1],
        2  => ['x' => 280,      'y' => $y1],
        3  => ['x' => 430,      'y' => $y1],
        4  => ['x' => $xR - 20, 'y' => $y2],
        5  => ['x' => 460,      'y' => $y2],
        6  => ['x' => 310,      'y' => $y2],
        7  => ['x' => 160,      'y' => $y2],
        8  => ['x' => 220,      'y' => $y3],
        9  => ['x' => 430,      'y' => $y3],
        10 => ['x' => $xR - 40, 'y' => $y4],
    ];

    $clusterOffsets = [
        1  => ['dx' =>   0, 'dy' => -58],
        2  => ['dx' =>   0, 'dy' => -58],
        3  => ['dx' =>   0, 'dy' => -58],
        4  => ['dx' =>   0, 'dy' => -60],
        5  => ['dx' =>   0, 'dy' =>  58],
        6  => ['dx' =>   0, 'dy' => -60],
        7  => ['dx' =>   0, 'dy' => -60],
        8  => ['dx' =>   0, 'dy' =>  60],
        9  => ['dx' =>   0, 'dy' =>  60],
        10 => ['dx' => -85, 'dy' =>   0],
    ];

    $canvasH    = 700;
    $maxVisible = 8;

    $avatarColors = [
        '#0076BF','#F59E0B','#10B981','#8B5CF6',
        '#EF4444','#EC4899','#06B6D4','#84CC16',
    ];

    $stageLabels = [
        1 => ['بصيص','✨','#3B82F6'],
        2 => ['بريق','⚡','#8B5CF6'],
        3 => ['ضياء','🌟','#F59E0B'],
        4 => ['وميض','🔥','#EF4444'],
        5 => ['نور','☀️','#10B981'],
    ];

    // Stage → which road rows/areas to spotlight
    // Each stage maps to: [row_segments, milestone_ids]
    $stageAreas = [
        1 => ['rows' => ['row1-left'],         'milestones' => [1,2]],
        2 => ['rows' => ['row1-right','row2-left'], 'milestones' => [3,4]],
        3 => ['rows' => ['row2-right'],        'milestones' => [5,6]],
        4 => ['rows' => ['row3'],              'milestones' => [7,8]],
        5 => ['rows' => ['row4'],              'milestones' => [9,10]],
    ];
@endphp

<div class="madmar-page">

    {{-- Hero --}}
    <div class="madmar-hero">
        <div class="hero-icon">🏟️</div>
        <div class="hero-info">
            <h1>مضمار مدارج النور — الـ 1000 متر</h1>
            <p>كل مشترك في موضعه على طريق النور</p>
        </div>
        <div class="hero-stat">
            <strong>{{ $total }}</strong>
            <span>مشترك في المضمار</span>
        </div>
    </div>

    {{-- Stage zoom buttons --}}
    <div class="stage-zoom-bar">
        @foreach($stageLabels as $sid => [$sname, $semoji, $scolor])
        <button class="stage-zoom-btn"
                id="zoom-btn-{{ $sid }}"
                style="background:{{ $scolor }}"
                onclick="zoomStage({{ $sid }})">
            {{ $semoji }} {{ $sname }}
            <small style="opacity:0.75;font-weight:600">{{ ($sid-1)*200+1 }}–{{ $sid*200 }}</small>
        </button>
        @endforeach

        <button class="stage-reset-btn" id="zoom-reset" onclick="zoomReset()">
            ↩ عرض الكل
        </button>
    </div>

    {{-- Filter --}}
    @if($role !== 4 && $role !== 5 && count($groupOpts) > 0)
    <div class="filter-bar">
        <label>🔍 تصفية حسب المجموعة:</label>
        <select wire:model.live="filterGroupId">
            <option value="">— جميع المجموعات —</option>
            @foreach($groupOpts as $gId => $gName)
                <option value="{{ $gId }}">{{ $gName }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Track --}}
    <div class="track-scroll" id="track-scroll-container">
    <div id="track-zoom-wrapper" class="track-zoom-wrapper"
         style="position:relative;width:680px;height:{{ $canvasH }}px;margin:0 auto;min-width:680px;">

        {{-- ══ ROAD SEGMENTS (with data-stage attributes) ══ --}}

        {{-- Row 1 bottom --}}
        <div class="road-segment" data-row="row1"
             style="left:{{ $xL }}px;top:{{ $y1-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>

        <div class="road-overlay" data-row="row1-left"
             style="left:{{ $xL }}px;top:{{ $y1-$rh/2 }}px;width:{{ ($xR-$xL)*0.5 }}px;height:{{ $rh }}px;background:#3B82F6;z-index:1;"></div>
        <div class="road-overlay" data-row="row1-right"
             style="left:{{ $xL+($xR-$xL)*0.5 }}px;top:{{ $y1-$rh/2 }}px;width:{{ ($xR-$xL)*0.5 }}px;height:{{ $rh }}px;background:#8B5CF6;z-index:1;"></div>

        {{-- Connector R --}}
        <div class="road-segment vertical" data-row="connector-r1"
             style="left:{{ $xR-$rw/2 }}px;top:{{ $y2-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y1-$y2+$rh }}px;"></div>

        {{-- Row 2 --}}
        <div class="road-segment" data-row="row2"
             style="left:{{ $xL }}px;top:{{ $y2-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>
        <div class="road-overlay" data-row="row2-left"
             style="left:{{ $xL }}px;top:{{ $y2-$rh/2 }}px;width:{{ ($xR-$xL)*0.5 }}px;height:{{ $rh }}px;background:#8B5CF6;z-index:1;"></div>
        <div class="road-overlay" data-row="row2-right"
             style="left:{{ $xL+($xR-$xL)*0.5 }}px;top:{{ $y2-$rh/2 }}px;width:{{ ($xR-$xL)*0.5 }}px;height:{{ $rh }}px;background:#F59E0B;z-index:1;"></div>

        {{-- Connector L --}}
        <div class="road-segment vertical" data-row="connector-l"
             style="left:{{ $xL-$rw/2+28 }}px;top:{{ $y3-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y2-$y3+$rh }}px;"></div>

        {{-- Row 3 --}}
        <div class="road-segment" data-row="row3"
             style="left:{{ $xL }}px;top:{{ $y3-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>
        <div class="road-overlay" data-row="row3"
             style="left:{{ $xL }}px;top:{{ $y3-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;background:#EF4444;z-index:1;"></div>

        {{-- Connector R2 --}}
        <div class="road-segment vertical" data-row="connector-r2"
             style="left:{{ $xR-$rw/2 }}px;top:{{ $y4-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y3-$y4+$rh }}px;"></div>

        {{-- Row 4 top --}}
        <div class="road-segment" data-row="row4"
             style="left:{{ $xL+100 }}px;top:{{ $y4-$rh/2 }}px;width:{{ $xR-$xL-100 }}px;height:{{ $rh }}px;"></div>
        <div class="road-overlay" data-row="row4"
             style="left:{{ $xL+100 }}px;top:{{ $y4-$rh/2 }}px;width:{{ $xR-$xL-100 }}px;height:{{ $rh }}px;background:#10B981;z-index:1;"></div>

        {{-- ══ SPOTLIGHT OVERLAYS (one per stage, hidden by default) ══ --}}
        {{-- Stage 1: row1 left half --}}
        <div class="stage-spotlight" id="spotlight-1"
             style="left:{{ $xL-4 }}px;top:{{ $y1-$rh/2-4 }}px;
                    width:{{ ($xR-$xL)*0.5+8 }}px;height:{{ $rh+8 }}px;
                    border:3px solid #3B82F6;
                    box-shadow:0 0 0 3px #3B82F688, 0 0 30px #3B82F655;"></div>

        {{-- Stage 2: row1 right + row2 left --}}
        <div class="stage-spotlight" id="spotlight-2"
             style="left:{{ $xL+($xR-$xL)*0.5-4 }}px;top:{{ $y2-$rh/2-4 }}px;
                    width:{{ ($xR-$xL)*0.5+8+$rw }}px;height:{{ $y1-$y2+$rh+8 }}px;
                    border:3px solid #8B5CF6;
                    box-shadow:0 0 0 3px #8B5CF688, 0 0 30px #8B5CF655;"></div>

        {{-- Stage 3: row2 right half --}}
        <div class="stage-spotlight" id="spotlight-3"
             style="left:{{ $xL+($xR-$xL)*0.5-4 }}px;top:{{ $y2-$rh/2-4 }}px;
                    width:{{ ($xR-$xL)*0.5+8 }}px;height:{{ $rh+8 }}px;
                    border:3px solid #F59E0B;
                    box-shadow:0 0 0 3px #F59E0B88, 0 0 30px #F59E0B55;"></div>

        {{-- Stage 4: row3 --}}
        <div class="stage-spotlight" id="spotlight-4"
             style="left:{{ $xL-4 }}px;top:{{ $y3-$rh/2-4 }}px;
                    width:{{ $xR-$xL+8 }}px;height:{{ $rh+8 }}px;
                    border:3px solid #EF4444;
                    box-shadow:0 0 0 3px #EF444488, 0 0 30px #EF444455;"></div>

        {{-- Stage 5: row4 --}}
        <div class="stage-spotlight" id="spotlight-5"
             style="left:{{ $xL+100-4 }}px;top:{{ $y4-$rh/2-4 }}px;
                    width:{{ $xR-$xL-100+8 }}px;height:{{ $rh+8 }}px;
                    border:3px solid #10B981;
                    box-shadow:0 0 0 3px #10B98188, 0 0 30px #10B98155;"></div>

        {{-- ══ START / FINISH ══ --}}
        <div class="start-marker" style="left:{{ $xL+10 }}px;top:{{ $y1 }}px;">الانطلاق 🚦</div>
        <div class="finish-trophy" style="left:{{ $mPos[10]['x'] }}px;top:{{ $y4-48 }}px;">🏆</div>
        <div class="finish-marker" style="left:{{ $mPos[10]['x'] }}px;top:{{ $y4+42 }}px;">فوز ⭐</div>

        {{-- ══ MILESTONES + SUBSCRIBER CLUSTERS ══ --}}
        @foreach($this->milestones as $m)
        @php
            $mx  = $mPos[$m['id']]['x'];
            $my  = $mPos[$m['id']]['y'];
            $odx = $clusterOffsets[$m['id']]['dx'];
            $ody = $clusterOffsets[$m['id']]['dy'];

            $subsHere = $byMilestone[$m['id']] ?? [];
            $subCount = count($subsHere);
            $visible  = array_slice($subsHere, 0, $maxVisible);
            $extra    = $subCount - count($visible);

            $stageColor = $milestoneStageColor[$m['id']] ?? '#0076BF';
            $stageId    = $milestoneStageId[$m['id']] ?? 1;
        @endphp

        {{-- Milestone pin --}}
        <div class="milestone-pin"
             data-stage="{{ $stageId }}"
             style="left:{{ $mx }}px;top:{{ $my }}px;z-index:15;">
            <div class="milestone-dot" style="background:{{ $stageColor }}">📍</div>
            <div class="milestone-label" style="background:{{ $stageColor }}">{{ $m['label'] }}</div>
            <div class="milestone-title">{{ $m['title'] }}</div>
        </div>

        {{-- Subscriber cluster --}}
        @if($subCount > 0)
        <div class="sub-cluster"
             data-stage="{{ $stageId }}"
             style="left:{{ $mx+$odx }}px;top:{{ $my+$ody }}px;">
            @foreach($visible as $vi => $sub)
            @php $bgColor = $avatarColors[$vi % count($avatarColors)]; @endphp
            <div class="sub-avatar" style="z-index:{{ 20+$vi }}">
                {{-- Inner circle: clips image, shows initials --}}
                <div class="sub-avatar-inner" style="background:{{ $bgColor }}">
                    @if($sub['image'])
                        <img src="{{ Storage::url($sub['image']) }}"
                             alt="{{ $sub['name'] }}"
                             onerror="this.style.display='none'">
                    @endif
                    @if(!$sub['image'])
                        {{ $sub['initials'] }}
                    @endif
                </div>

                {{-- Hover card sits outside the clipped circle --}}
                <div class="hover-card">
                    <div class="hover-card-top">
                        <div class="hover-avatar" style="background:{{ $bgColor }}">
                            @if($sub['image'])
                                <img src="{{ Storage::url($sub['image']) }}" alt="">
                            @else
                                {{ $sub['initials'] }}
                            @endif
                        </div>
                        <div>
                            <div class="hover-name">{{ $sub['name'] }}</div>
                            <div class="hover-group">{{ $sub['group'] }}</div>
                        </div>
                    </div>
                    <div class="hover-row">
                        <span class="lbl">النقاط</span>
                        <span class="val" style="color:{{ $stageColor }}">{{ $sub['points'] }} / 1000</span>
                    </div>
                    <div class="hover-row">
                        <span class="lbl">المرحلة</span>
                        <span class="val">{{ $sub['stage'] }}</span>
                    </div>
                    <div class="hover-row">
                        <span class="lbl">المستوى</span>
                        <span class="val">{{ $m['title'] }}</span>
                    </div>
                    <div class="hover-row">
                        <span class="lbl">الدراسة</span>
                        <span class="val">{{ $sub['study'] }}</span>
                    </div>
                    <div class="hover-row">
                        <span class="lbl">تاريخ الانضمام</span>
                        <span class="val">{{ $sub['join_date'] }}</span>
                    </div>
                    <div class="hover-pts-bar">
                        <div class="hover-pts-fill" style="width:{{ $sub['pct'] }}%;background:{{ $stageColor }}"></div>
                    </div>
                    <div style="text-align:center;font-size:0.62rem;color:#94A3B8;margin-top:3px">
                        {{ $sub['pct'] }}% من المضمار
                    </div>
                </div>
            </div>
            @endforeach

            @if($extra > 0)
            <div class="sub-count-badge">+{{ $extra }}</div>
            @endif
        </div>
        @endif

        @endforeach

    </div>
    </div>

    {{-- Legend --}}
    @if($total > 0)
    <div class="legend-bar">
        <h4>🗺️ دليل المضمار</h4>
        @foreach($this->milestones as $m)
        @php $cnt = count($byMilestone[$m['id']] ?? []); @endphp
        <div class="legend-item">
            <div class="legend-dot" style="background:{{ $milestoneStageColor[$m['id']] }}"></div>
            <span><strong>{{ $m['label'] }}</strong> — {{ $m['title'] }}</span>
            @if($cnt > 0)
                <span class="legend-cnt">{{ $cnt }}</span>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($total === 0)
    <div class="empty-track">
        <div class="e-icon">🏜️</div>
        <p>لا يوجد مشتركون في المضمار حالياً</p>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════
     ZOOM ENGINE
══════════════════════════════════════ --}}
<script>
(function() {

    // Stage → zoom config: scale + translate-Y to center that row
    // Canvas is 700px tall. Rows at y1=580, y2=420, y3=260, y4=100
    const stageConfig = {
        1: { scale: 1.9, ty: -310, color: '#3B82F6' }, // row1 left
        2: { scale: 1.7, ty: -200, color: '#8B5CF6' }, // row1-right + row2-left
        3: { scale: 1.9, ty: -185, color: '#F59E0B' }, // row2 right
        4: { scale: 1.9, ty:  -60, color: '#EF4444' }, // row3
        5: { scale: 2.2, ty:   80, color: '#10B981' }, // row4 top
    };

    let activeStage = null;

    window.zoomStage = function(stageId) {
        // Toggle off if clicking same stage
        if (activeStage === stageId) { zoomReset(); return; }
        activeStage = stageId;

        const cfg     = stageConfig[stageId];
        const wrapper = document.getElementById('track-zoom-wrapper');

        // Apply zoom + translate
        wrapper.style.transform = `scale(${cfg.scale}) translateY(${cfg.ty}px)`;
        wrapper.classList.add('has-focus');

        // Spotlight
        for (let i = 1; i <= 5; i++) {
            const sp = document.getElementById('spotlight-' + i);
            if (sp) sp.classList.toggle('lit', i === stageId);
        }

        // Dim/lit milestones + clusters
        document.querySelectorAll('.milestone-pin, .sub-cluster').forEach(el => {
            const s = parseInt(el.dataset.stage);
            el.classList.toggle('dimmed', s !== stageId);
            el.classList.toggle('lit',    s === stageId);
        });

        // Update buttons
        document.querySelectorAll('.stage-zoom-btn').forEach(btn => {
            btn.classList.toggle('active', btn.id === 'zoom-btn-' + stageId);
        });
        document.getElementById('zoom-reset').classList.add('visible');

        // Scroll track into view smoothly
        document.getElementById('track-scroll-container')
            .scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    window.zoomReset = function() {
        activeStage = null;

        const wrapper = document.getElementById('track-zoom-wrapper');
        wrapper.style.transform = 'scale(1) translateY(0px)';
        wrapper.classList.remove('has-focus');

        // Remove all spotlights
        document.querySelectorAll('.stage-spotlight').forEach(sp => sp.classList.remove('lit'));

        // Restore all elements
        document.querySelectorAll('.milestone-pin, .sub-cluster').forEach(el => {
            el.classList.remove('dimmed', 'lit');
        });

        // Reset buttons
        document.querySelectorAll('.stage-zoom-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('zoom-reset').classList.remove('visible');
    };

})();
</script>

</x-filament-panels::page>