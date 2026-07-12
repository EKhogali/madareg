<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');

:root {
    --road-bg:  #2D2D2D;
    --gold:     #F4A623;
    --blue:     #0076BF;
    --navy:     #1A2B4A;
    --cyan:     #00BCD4;
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

/* ── Track scroll ── */
.track-scroll { overflow-x:auto; padding-bottom:1rem; }

/* ── Road segment ── */
.road-segment {
    position:absolute;
    background:var(--road-bg);
    border-radius:28px;
    box-shadow:0 4px 18px rgba(0,0,0,0.22);
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
}
.start-marker  { background:#EF4444; color:white; }
.finish-marker { background:var(--gold); color:white; font-size:1rem; }
.finish-trophy { font-size:2rem; position:absolute; z-index:13; transform:translate(-50%,-50%); }

/* ══════════════════════════════
   SUBSCRIBER CLUSTER
══════════════════════════════ */
.sub-cluster {
    position:absolute; display:flex; flex-wrap:wrap; gap:3px;
    z-index:20; transform:translate(-50%,-50%);
    max-width:90px; justify-content:center;
}

.sub-avatar {
    position:relative; width:34px; height:34px;
    border-radius:50%; cursor:pointer; flex-shrink:0;
    transition:transform 0.2s ease; overflow:visible;
}
.sub-avatar:hover { transform:scale(1.3); z-index:100; }

.sub-avatar-inner {
    position:absolute; top:0; left:0;
    width:100%; height:100%; border-radius:50%;
    border:2.5px solid white;
    box-shadow:0 2px 6px rgba(0,0,0,0.22);
    display:flex; align-items:center; justify-content:center;
    font-size:0.75rem; font-weight:800; color:white;
    overflow:hidden; background:inherit;
}
.sub-avatar-inner img {
    width:100%; height:100%; object-fit:cover;
    border-radius:50%; display:block;
}

/* ── +N expandable badge ── */
.sub-more-badge {
    width:34px; height:34px; border-radius:50%;
    background:var(--navy); color:white;
    border:2.5px solid white;
    display:flex; align-items:center; justify-content:center;
    font-size:0.62rem; font-weight:800;
    box-shadow:0 2px 6px rgba(0,0,0,0.22);
    flex-shrink:0; cursor:pointer;
    transition:transform 0.2s ease, background 0.2s ease;
    position:relative;
}
.sub-more-badge:hover { transform:scale(1.15); background:var(--blue); }

/* ── Expanded popup panel ── */
.expanded-popup {
    display:none;
    /* Fixed to viewport so it's always in front and responsive */
    position:fixed;
    top:50%; left:50%;
    transform:translate(-50%, -50%);
    background:white;
    border-radius:1.25rem;
    box-shadow:0 24px 80px rgba(0,0,0,0.35);
    padding:0;
    z-index:99999;
    border:1.5px solid #E2E8F0;
    width:min(520px, 90vw);
    max-height:80vh;
    overflow:hidden;
    animation:popup-in 0.22s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes popup-in {
    from { opacity:0; transform:translate(-50%,-50%) scale(0.88); }
    to   { opacity:1; transform:translate(-50%,-50%) scale(1); }
}
.expanded-popup.open { display:block; }

/* Backdrop overlay */
.popup-backdrop {
    display:none;
    position:fixed; inset:0;
    background:rgba(0,0,0,0.45);
    z-index:99998;
    backdrop-filter:blur(3px);
    animation:fade-in 0.2s ease;
}
@keyframes fade-in { from{opacity:0} to{opacity:1} }
.popup-backdrop.open { display:block; }

.popup-header {
    font-size:0.9rem; font-weight:900; color:var(--navy);
    padding:1rem 1.25rem 0.85rem;
    border-bottom:1px solid #F1F5F9;
    display:flex; justify-content:space-between; align-items:center;
    background:linear-gradient(135deg, #F8FAFC, #EFF6FF);
    border-radius:1.25rem 1.25rem 0 0;
    position:sticky; top:0; z-index:1;
}
.popup-header-left { display:flex; flex-direction:column; gap:0.15rem; }
.popup-header-title { font-size:1rem; font-weight:900; color:var(--navy); }
.popup-header-sub   { font-size:0.72rem; color:#64748B; }
.popup-close {
    cursor:pointer; color:#94A3B8;
    font-size:1.2rem; line-height:1;
    width:28px; height:28px;
    display:flex; align-items:center; justify-content:center;
    border-radius:50%; transition:all 0.15s;
    background:#F1F5F9;
}
.popup-close:hover { color:white; background:#EF4444; }

.popup-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap:0.85rem;
    padding:1rem 1.25rem 1.25rem;
    overflow-y:auto;
    max-height:calc(80vh - 70px);
}
.popup-item {
    display:flex; flex-direction:column; align-items:center; gap:5px;
    cursor:pointer;
    padding:0.6rem 0.4rem;
    border-radius:0.75rem;
    transition:background 0.15s, transform 0.15s;
    position:relative;
}
.popup-item:hover { background:#F0F9FF; transform:translateY(-2px); }
.popup-item:hover .popup-item-tooltip { display:block; }

/* Tooltip on popup avatar hover */
.popup-item-tooltip {
    display:none;
    position:absolute;
    bottom:calc(100% + 6px);
    left:50%; transform:translateX(-50%);
    background:var(--navy);
    color:white;
    font-size:0.65rem;
    font-weight:700;
    padding:0.3rem 0.6rem;
    border-radius:0.4rem;
    white-space:nowrap;
    z-index:10;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
    pointer-events:none;
}
.popup-item-tooltip::after {
    content:'';
    position:absolute;
    top:100%; left:50%; transform:translateX(-50%);
    border:5px solid transparent;
    border-top-color:var(--navy);
}

/* Detail side panel inside popup */
.popup-detail-panel {
    display:none;
    border-top:1px solid #F1F5F9;
    padding:0.85rem 1.25rem;
    background:#F8FAFC;
    animation:slide-up 0.2s ease;
}
@keyframes slide-up {
    from { opacity:0; transform:translateY(6px); }
    to   { opacity:1; transform:translateY(0); }
}
.popup-detail-panel.open { display:block; }
.popup-detail-top {
    display:flex; align-items:center; gap:0.75rem;
    margin-bottom:0.75rem;
}
.popup-detail-avatar {
    width:48px; height:48px; border-radius:50%;
    border:3px solid white; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; font-weight:800; color:white;
    box-shadow:0 3px 10px rgba(0,0,0,0.18); flex-shrink:0;
}
.popup-detail-avatar img { width:100%; height:100%; object-fit:cover; }
.popup-detail-name  { font-size:0.9rem; font-weight:900; color:var(--navy); }
.popup-detail-group { font-size:0.72rem; color:#94A3B8; }
.popup-detail-rows  { display:grid; grid-template-columns:1fr 1fr; gap:0.4rem 1rem; }
.popup-detail-row   { display:flex; flex-direction:column; gap:1px; }
.popup-detail-lbl   { font-size:0.62rem; color:#94A3B8; }
.popup-detail-val   { font-size:0.75rem; font-weight:700; color:var(--navy); }
.popup-detail-bar   { margin-top:0.6rem; background:#E2E8F0; border-radius:999px; height:6px; overflow:hidden; }
.popup-detail-fill  { height:100%; border-radius:999px; background:linear-gradient(90deg,var(--blue),#00A8E8); }
.popup-detail-pct   { text-align:center; font-size:0.65rem; color:#94A3B8; margin-top:3px; }
.popup-detail-close { float:left; cursor:pointer; font-size:0.7rem; color:#94A3B8; margin-top:-1.5rem; }
.popup-detail-close:hover { color:var(--navy); }

.popup-avatar {
    width:52px; height:52px; border-radius:50%;
    border:3px solid white; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; font-weight:800; color:white;
    box-shadow:0 3px 10px rgba(0,0,0,0.18);
    flex-shrink:0;
}
.popup-avatar img { width:100%; height:100%; object-fit:cover; }
.popup-name {
    font-size:0.68rem; color:var(--navy); font-weight:700;
    text-align:center; line-height:1.3;
    width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.popup-pts {
    font-size:0.65rem; color:var(--blue); font-weight:800;
    background:#EFF6FF; border-radius:999px; padding:0.1rem 0.5rem;
}

/* ── Hover card on individual avatars ── */
.hover-card {
    display:none; position:absolute;
    bottom:calc(100% + 10px); left:50%; transform:translateX(-50%);
    width:185px; background:white; border-radius:0.85rem;
    box-shadow:0 8px 28px rgba(0,0,0,0.18);
    padding:0.75rem; z-index:300; pointer-events:none;
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
.hover-ha {
    width:36px; height:36px; border-radius:50%;
    flex-shrink:0; border:2px solid #E2E8F0;
    display:flex; align-items:center; justify-content:center;
    font-size:0.9rem; font-weight:800; color:white; overflow:hidden;
}
.hover-ha img { width:100%; height:100%; object-fit:cover; }
.hover-name  { font-size:0.78rem; font-weight:800; color:var(--navy); line-height:1.2; }
.hover-group { font-size:0.65rem; color:#94A3B8; }
.hover-row   { display:flex; justify-content:space-between; font-size:0.68rem; margin-bottom:0.2rem; }
.hover-row .lbl { color:#94A3B8; }
.hover-row .val { color:var(--navy); font-weight:700; }
.hover-bar  { margin-top:0.45rem; background:#F1F5F9; border-radius:999px; height:5px; overflow:hidden; }
.hover-fill { height:100%; border-radius:999px; background:var(--blue); }

/* ── Legend ── */
.legend-bar {
    background:white; border-radius:1rem;
    padding:1rem 1.5rem; margin-top:1.5rem;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    display:flex; flex-wrap:wrap; gap:0.75rem 1.5rem; align-items:center;
}
.legend-bar h4 { font-weight:800; color:var(--navy); font-size:0.9rem; margin:0 0 0.5rem; width:100%; }
.legend-item { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#475569; }
.legend-dot  { width:12px; height:12px; border-radius:50%; background:var(--gold); flex-shrink:0; }
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
    $maxVisible = 6;

    $avatarColors = [
        '#0076BF','#F59E0B','#10B981','#8B5CF6',
        '#EF4444','#EC4899','#06B6D4','#84CC16',
        '#F97316','#6366F1','#14B8A6','#A855F7',
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
    <div class="track-scroll">
    <div style="position:relative;width:680px;height:{{ $canvasH }}px;margin:0 auto;min-width:680px;">

        {{-- ══ ROAD SEGMENTS ══ --}}
        <div class="road-segment"
             style="left:{{ $xL }}px;top:{{ $y1-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>

        <div class="road-segment vertical"
             style="left:{{ $xR-$rw/2 }}px;top:{{ $y2-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y1-$y2+$rh }}px;"></div>

        <div class="road-segment"
             style="left:{{ $xL }}px;top:{{ $y2-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>

        <div class="road-segment vertical"
             style="left:{{ $xL-$rw/2+28 }}px;top:{{ $y3-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y2-$y3+$rh }}px;"></div>

        <div class="road-segment"
             style="left:{{ $xL }}px;top:{{ $y3-$rh/2 }}px;width:{{ $xR-$xL }}px;height:{{ $rh }}px;"></div>

        <div class="road-segment vertical"
             style="left:{{ $xR-$rw/2 }}px;top:{{ $y4-$rh/2 }}px;width:{{ $rw }}px;height:{{ $y3-$y4+$rh }}px;"></div>

        <div class="road-segment"
             style="left:{{ $xL+100 }}px;top:{{ $y4-$rh/2 }}px;width:{{ $xR-$xL-100 }}px;height:{{ $rh }}px;"></div>

        {{-- ══ START / FINISH ══ --}}
        <div class="start-marker" style="left:{{ $xL+10 }}px;top:{{ $y1 }}px;">الانطلاق 🚦</div>
        <div class="finish-trophy" style="left:{{ $mPos[10]['x'] }}px;top:{{ $y4-48 }}px;">🏆</div>
        <div class="finish-marker" style="left:{{ $mPos[10]['x'] }}px;top:{{ $y4+42 }}px;">فوز ⭐</div>

        {{-- ══ MILESTONES + SUBSCRIBER CLUSTERS ══ --}}
        @foreach($this->milestones as $m)
        @php
            $mx       = $mPos[$m['id']]['x'];
            $my       = $mPos[$m['id']]['y'];
            $odx      = $clusterOffsets[$m['id']]['dx'];
            $ody      = $clusterOffsets[$m['id']]['dy'];
            $subsHere = $byMilestone[$m['id']] ?? [];
            $subCount = count($subsHere);
            $visible  = array_slice($subsHere, 0, $maxVisible);
            $hidden   = array_slice($subsHere, $maxVisible);
            $extra    = count($hidden);
            $popupId  = 'popup-m' . $m['id'];
        @endphp

        {{-- Milestone pin --}}
        <div class="milestone-pin" style="left:{{ $mx }}px;top:{{ $my }}px;z-index:15;">
            <div class="milestone-dot">📍</div>
            <div class="milestone-label">{{ $m['label'] }}</div>
            <div class="milestone-title">{{ $m['title'] }}</div>
        </div>

        {{-- Subscriber cluster --}}
        @if($subCount > 0)
        <div class="sub-cluster" style="left:{{ $mx+$odx }}px;top:{{ $my+$ody }}px;">

            {{-- Visible avatars --}}
            @foreach($visible as $vi => $sub)
            @php $bgColor = $avatarColors[$vi % count($avatarColors)]; @endphp
            <div class="sub-avatar" style="z-index:{{ 20+$vi }}">
                <div class="sub-avatar-inner" style="background:{{ $bgColor }}">
                    @if($sub['image'])
                        <img src="{{ Storage::url($sub['image']) }}"
                             alt="{{ $sub['name'] }}"
                             onerror="this.style.display='none'">
                    @endif
                    @if(!$sub['image']) {{ $sub['initials'] }} @endif
                </div>

                {{-- Hover card --}}
                <div class="hover-card">
                    <div class="hover-card-top">
                        <div class="hover-ha" style="background:{{ $bgColor }}">
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
                        <span class="val" style="color:var(--blue)">{{ $sub['points'] }} / 1000</span>
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
                    <div class="hover-bar">
                        <div class="hover-fill" style="width:{{ $sub['pct'] }}%"></div>
                    </div>
                    <div style="text-align:center;font-size:0.62rem;color:#94A3B8;margin-top:3px">
                        {{ $sub['pct'] }}% من المضمار
                    </div>
                </div>
            </div>
            @endforeach

            {{-- +N expandable badge — stores data as JSON, popup rendered outside canvas --}}
            @if($extra > 0)
            @php
                $hiddenData = collect($hidden)->map(function($sub, $hi) use ($avatarColors, $maxVisible) {
                    return [
                        'name'      => $sub['name'],
                        'initials'  => $sub['initials'],
                        'points'    => $sub['points'],
                        'image'     => $sub['image'] ? Storage::url($sub['image']) : null,
                        'color'     => $avatarColors[($hi + $maxVisible) % count($avatarColors)],
                        'group'     => $sub['group']     ?? '—',
                        'stage'     => $sub['stage']     ?? '—',
                        'study'     => $sub['study']     ?? '—',
                        'join_date' => $sub['join_date'] ?? '—',
                    ];
                })->values()->toJson();
            @endphp
            <div class="sub-more-badge"
                 onclick="openGlobalPopup(this, event)"
                 data-popup-id="{{ $popupId }}"
                 data-title="{{ $m['label'] }} — {{ $m['title'] }}"
                 data-sub="{{ $extra }} مشترك إضافي · {{ $subCount }} إجمالاً"
                 data-hidden='{!! htmlspecialchars($hiddenData, ENT_QUOTES) !!}'>
                +{{ $extra }}
            </div>
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
            <div class="legend-dot"></div>
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

{{-- Global backdrop --}}
<div class="popup-backdrop" id="popup-backdrop" onclick="closeAllPopups()"></div>

{{-- Global popup portal — rendered OUTSIDE any transform/overflow context --}}
<div class="expanded-popup" id="global-popup">
    <div class="popup-header">
        <div class="popup-header-left">
            <span class="popup-header-title" id="global-popup-title"></span>
            <span class="popup-header-sub"   id="global-popup-sub"></span>
        </div>
        <span class="popup-close" onclick="closeAllPopups()">✕</span>
    </div>
    <div class="popup-grid" id="global-popup-grid"></div>

    {{-- Detail panel: shown when a subscriber is clicked --}}
    <div class="popup-detail-panel" id="popup-detail-panel">
        <span class="popup-detail-close" onclick="closeDetail()">▲ إخفاء التفاصيل</span>
        <div class="popup-detail-top">
            <div class="popup-detail-avatar" id="detail-avatar"></div>
            <div>
                <div class="popup-detail-name"  id="detail-name"></div>
                <div class="popup-detail-group" id="detail-group"></div>
            </div>
        </div>
        <div class="popup-detail-rows">
            <div class="popup-detail-row">
                <span class="popup-detail-lbl">النقاط</span>
                <span class="popup-detail-val" id="detail-pts"></span>
            </div>
            <div class="popup-detail-row">
                <span class="popup-detail-lbl">المرحلة</span>
                <span class="popup-detail-val" id="detail-stage"></span>
            </div>
            <div class="popup-detail-row">
                <span class="popup-detail-lbl">الدراسة</span>
                <span class="popup-detail-val" id="detail-study"></span>
            </div>
            <div class="popup-detail-row">
                <span class="popup-detail-lbl">تاريخ الانضمام</span>
                <span class="popup-detail-val" id="detail-join"></span>
            </div>
        </div>
        <div class="popup-detail-bar">
            <div class="popup-detail-fill" id="detail-fill"></div>
        </div>
        <div class="popup-detail-pct" id="detail-pct"></div>
    </div>
</div>

<script>
function openGlobalPopup(badge, event) {
    event.stopPropagation();

    const popup    = document.getElementById('global-popup');
    const backdrop = document.getElementById('popup-backdrop');

    // If same badge clicked again → close
    if (popup.classList.contains('open') && popup.dataset.source === badge.dataset.popupId) {
        closeAllPopups(); return;
    }

    // Fill popup content from badge data
    document.getElementById('global-popup-title').textContent = badge.dataset.title;
    document.getElementById('global-popup-sub').textContent   = badge.dataset.sub;

    const hidden = JSON.parse(badge.dataset.hidden);
    const grid   = document.getElementById('global-popup-grid');
    grid.innerHTML = '';

    hidden.forEach(sub => {
        const item = document.createElement('div');
        item.className = 'popup-item';
        item.onclick = (e) => { e.stopPropagation(); showDetail(sub); };

        const av = document.createElement('div');
        av.className = 'popup-avatar';
        av.style.background = sub.color;

        if (sub.image) {
            const img = document.createElement('img');
            img.src = sub.image;
            img.alt = sub.name;
            img.onerror = () => { img.remove(); av.textContent = sub.initials; };
            av.appendChild(img);
        } else {
            av.textContent = sub.initials;
        }

        const name = document.createElement('div');
        name.className = 'popup-name';
        name.textContent = sub.name;

        const pts = document.createElement('div');
        pts.className = 'popup-pts';
        pts.textContent = sub.points + 'ن';

        // Tooltip
        const tip = document.createElement('div');
        tip.className = 'popup-item-tooltip';
        tip.textContent = sub.name + ' — ' + sub.points + ' نقطة';

        item.appendChild(av);
        item.appendChild(name);
        item.appendChild(pts);
        item.appendChild(tip);
        grid.appendChild(item);
    });

    popup.dataset.source = badge.dataset.popupId;
    popup.classList.add('open');
    backdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function showDetail(sub) {
    const panel = document.getElementById('popup-detail-panel');

    // Avatar
    const av = document.getElementById('detail-avatar');
    av.style.background = sub.color;
    av.innerHTML = '';
    if (sub.image) {
        const img = document.createElement('img');
        img.src = sub.image;
        img.onerror = () => { img.remove(); av.textContent = sub.initials; };
        av.appendChild(img);
    } else {
        av.textContent = sub.initials;
    }

    document.getElementById('detail-name').textContent  = sub.name;
    document.getElementById('detail-group').textContent = sub.group  || '—';
    document.getElementById('detail-pts').textContent   = sub.points + ' / 1000 نقطة';
    document.getElementById('detail-stage').textContent = sub.stage  || '—';
    document.getElementById('detail-study').textContent = sub.study  || '—';
    document.getElementById('detail-join').textContent  = sub.join_date || '—';

    const pct = Math.min(100, Math.round((sub.points / 1000) * 100 * 10) / 10);
    document.getElementById('detail-fill').style.width = pct + '%';
    document.getElementById('detail-pct').textContent  = pct + '% من المضمار';

    panel.classList.add('open');

    // Scroll detail into view
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function closeDetail() {
    document.getElementById('popup-detail-panel').classList.remove('open');
}

function closeAllPopups() {
    const popup    = document.getElementById('global-popup');
    const backdrop = document.getElementById('popup-backdrop');
    if (popup)    { popup.classList.remove('open'); delete popup.dataset.source; }
    if (backdrop) { backdrop.classList.remove('open'); }
    document.body.style.overflow = '';
    const detail = document.getElementById('popup-detail-panel');
    if (detail)   { detail.classList.remove('open'); }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllPopups(); });
</script>

</x-filament-panels::page>