<x-filament-panels::page>

<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');

:root {
    --gold:    #F4A623;
    --navy:    #1A2B4A;
    --blue:    #0076BF;
    --sky:     #00A8E8;
    --teal:    #0D9488;
    --purple:  #7C3AED;
    --rose:    #E11D48;
    --amber:   #D97706;
    --green:   #16A34A;
    --slate:   #475569;
}

*, *::before, *::after { box-sizing: border-box; }

.sv-page {
    font-family: 'Tajawal', sans-serif;
    direction: rtl;
    padding: 0 0 4rem;
    background: linear-gradient(160deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);
    min-height: 100vh;
}

/* ══════════════════════════════
   HERO
══════════════════════════════ */
.sv-hero {
    position: relative;
    border-radius: 1.5rem;
    overflow: hidden;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #0A1628 0%, #0076BF 55%, #F4A623 100%);
    display: flex; align-items: center; gap: 2rem;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
}
.sv-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: repeating-linear-gradient(
        45deg,
        transparent, transparent 20px,
        rgba(255,255,255,0.02) 20px, rgba(255,255,255,0.02) 21px
    );
    pointer-events: none;
}
.sv-hero-icon {
    font-size: 4rem; z-index: 1; flex-shrink: 0;
    animation: trophy-bounce 2s ease-in-out infinite;
    filter: drop-shadow(0 4px 12px rgba(244,166,35,0.5));
}
@keyframes trophy-bounce {
    0%,100% { transform: translateY(0) rotate(-3deg); }
    50%      { transform: translateY(-8px) rotate(3deg); }
}
.sv-hero-text { flex: 1; z-index: 1; }
.sv-hero-text h1 {
    font-size: 1.9rem; font-weight: 900;
    color: var(--gold); margin: 0 0 0.3rem;
    text-shadow: 0 2px 12px rgba(0,0,0,0.4);
}
.sv-hero-text p { color: rgba(255,255,255,0.7); font-size: 0.95rem; margin: 0; }

.sv-hero-stats {
    display: flex; gap: 1rem; z-index: 1; flex-shrink: 0;
}
.sv-stat {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 1rem; padding: 0.75rem 1.25rem;
    text-align: center; backdrop-filter: blur(8px);
}
.sv-stat strong {
    display: block; font-size: 1.8rem; font-weight: 900;
    color: var(--gold); line-height: 1;
}
.sv-stat span { color: rgba(255,255,255,0.65); font-size: 0.72rem; }

/* ══════════════════════════════
   MILESTONE HEADER BAR
══════════════════════════════ */
.track-wrapper {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1.5rem;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}

.milestone-header {
    display: flex;
    margin-bottom: 0.5rem;
    padding-right: 220px; /* space for name col */
    position: relative;
}
.milestone-tick {
    flex: 1;
    text-align: center;
    font-size: 0.6rem;
    font-weight: 800;
    color: rgba(255,255,255,0.35);
    position: relative;
}
.milestone-tick::before {
    content: '';
    position: absolute;
    top: 100%; left: 50%;
    width: 1px; height: 8px;
    background: rgba(255,255,255,0.15);
    transform: translateX(-50%);
}

/* ══════════════════════════════
   RACE LANES
══════════════════════════════ */
.race-lanes { display: flex; flex-direction: column; gap: 0.6rem; }

.lane {
    display: flex;
    align-items: center;
    gap: 0;
    border-radius: 0.85rem;
    overflow: hidden;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    transition: border-color 0.3s ease, background 0.3s ease;
    position: relative;
}
.lane:hover {
    border-color: rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.07);
}
.lane.is-me {
    border-color: var(--gold) !important;
    background: rgba(244,166,35,0.08) !important;
    box-shadow: 0 0 0 1px var(--gold), 0 4px 20px rgba(244,166,35,0.2);
}

/* ── Name column ── */
.lane-name-col {
    width: 220px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.75rem 1rem;
    border-left: 1px solid rgba(255,255,255,0.07);
}
.lane-medal {
    font-size: 1.2rem;
    flex-shrink: 0;
    width: 28px;
    text-align: center;
}
.lane-rank-num {
    font-size: 0.72rem;
    font-weight: 800;
    color: rgba(255,255,255,0.35);
    width: 28px;
    text-align: center;
}
.lane-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; font-weight: 800; color: white;
    flex-shrink: 0;
    border: 2px solid rgba(255,255,255,0.2);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.lane-info { flex: 1; min-width: 0; }
.lane-info-name {
    font-size: 0.85rem; font-weight: 800;
    color: white;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.lane-info-role {
    font-size: 0.65rem; color: rgba(255,255,255,0.45);
}
.lane-me-badge {
    font-size: 0.58rem; font-weight: 800;
    background: var(--gold); color: var(--navy);
    border-radius: 999px; padding: 0.1rem 0.4rem;
    margin-top: 2px; display: inline-block;
}

/* ── Track column ── */
.lane-track-col {
    flex: 1;
    padding: 0.75rem 1rem;
    position: relative;
}

/* Background grid lines at each milestone */
.lane-grid {
    position: absolute;
    inset: 0;
    display: flex;
    pointer-events: none;
}
.lane-grid-line {
    flex: 1;
    border-right: 1px dashed rgba(255,255,255,0.06);
}

/* The road strip */
.lane-road {
    position: relative;
    height: 28px;
    background: rgba(0,0,0,0.3);
    border-radius: 999px;
    overflow: visible;
    border: 1px solid rgba(255,255,255,0.08);
}

/* Filled progress */
.lane-fill {
    position: absolute;
    top: 0; right: 0; /* RTL: fill from right */
    height: 100%;
    border-radius: 999px;
    transition: width 1.4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
    width: 0%;
}
.lane-fill::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    border-radius: 999px;
    animation: shimmer 2.5s infinite;
}
@keyframes shimmer {
    0%   { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

/* Runner emoji */
.lane-runner {
    position: absolute;
    top: 50%; transform: translateY(-55%);
    font-size: 1.1rem;
    transition: right 1.4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
    animation: runner-step 0.4s steps(2) infinite;
    z-index: 2;
    right: 0%;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
}
@keyframes runner-step {
    0%   { transform: translateY(-55%) scaleY(1); }
    50%  { transform: translateY(-60%) scaleY(1.05); }
    100% { transform: translateY(-55%) scaleY(1); }
}

/* Points label on the road */
.lane-pts-label {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    right: 8px;
    font-size: 0.6rem; font-weight: 800;
    color: rgba(255,255,255,0.5);
    pointer-events: none;
    z-index: 1;
}

/* Below road: milestone title + pct */
.lane-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.3rem;
}
.lane-milestone {
    font-size: 0.65rem; font-weight: 700;
    color: rgba(255,255,255,0.5);
}
.lane-pct {
    font-size: 0.65rem; font-weight: 800;
}

/* ══════════════════════════════
   FINISH LINE
══════════════════════════════ */
.finish-line {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: repeating-linear-gradient(
        180deg,
        white 0px, white 6px,
        black 6px, black 12px
    );
    opacity: 0.3;
    pointer-events: none;
    z-index: 3;
}

/* ══════════════════════════════
   PODIUM (top 3)
══════════════════════════════ */
.podium-section {
    margin-bottom: 1.5rem;
}
.podium-title {
    font-size: 0.8rem; font-weight: 800;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 0.85rem;
}
.podium-row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.podium-card {
    flex: 1; min-width: 140px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 1rem;
    padding: 1rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease;
}
.podium-card:hover { transform: translateY(-3px); }
.podium-card.rank-1 {
    background: linear-gradient(135deg, rgba(244,166,35,0.15), rgba(244,166,35,0.05));
    border-color: rgba(244,166,35,0.4);
}
.podium-card.rank-2 {
    background: linear-gradient(135deg, rgba(148,163,184,0.15), rgba(148,163,184,0.05));
    border-color: rgba(148,163,184,0.3);
}
.podium-card.rank-3 {
    background: linear-gradient(135deg, rgba(180,83,9,0.15), rgba(180,83,9,0.05));
    border-color: rgba(180,83,9,0.3);
}
.podium-medal { font-size: 2rem; display: block; margin-bottom: 0.4rem; }
.podium-name  { font-size: 0.85rem; font-weight: 800; color: white; margin-bottom: 0.2rem; }
.podium-pts   { font-size: 1.1rem; font-weight: 900; }
.podium-card.rank-1 .podium-pts { color: var(--gold); }
.podium-card.rank-2 .podium-pts { color: #94A3B8; }
.podium-card.rank-3 .podium-pts { color: #B45309; }
.podium-milestone {
    font-size: 0.65rem; color: rgba(255,255,255,0.45);
    margin-top: 0.2rem;
}

/* ══════════════════════════════
   EMPTY STATE
══════════════════════════════ */
.empty-sv {
    text-align: center; padding: 4rem 2rem;
    color: rgba(255,255,255,0.3);
}
.empty-sv .e-icon { font-size: 4rem; margin-bottom: 1rem; display: block; }

@media (max-width: 640px) {
    .sv-hero { flex-direction: column; text-align: center; }
    .sv-hero-stats { width: 100%; }
    .lane-name-col { width: 140px; }
    .milestone-header { padding-right: 140px; }
    .podium-card { min-width: 100px; }
}
</style>

@php
    $u    = auth()->user();
    $role = (int) $u?->role;
    $sups = $this->supervisors;
    $total = count($sups);
    $top3  = array_slice($sups, 0, 3);
    $rest  = array_slice($sups, 3);

    $milestones = $this->getMilestones();

    // Lane colors per rank (logo palette)
    $laneColors = [
        '#F4A623', // gold
        '#0076BF', // blue
        '#0D9488', // teal
        '#7C3AED', // purple
        '#E11D48', // rose
        '#D97706', // amber
        '#16A34A', // green
        '#00A8E8', // sky
        '#9333EA', // violet
        '#0F766E', // dark teal
    ];
@endphp

<div class="sv-page" x-data x-init="
    setTimeout(() => {
        document.querySelectorAll('.lane-fill').forEach(el => {
            el.style.width = el.dataset.pct + '%';
        });
        document.querySelectorAll('.lane-runner').forEach(el => {
            el.style.right = 'calc(' + el.dataset.pct + '% - 14px)';
        });
    }, 300);
">

    {{-- Hero --}}
    <div class="sv-hero">
        <div class="sv-hero-icon">🏆</div>
        <div class="sv-hero-text">
            <h1>مضمار المشرفين — مدارج النور</h1>
            <p>سباق الإنجاز نحو الـ 1000 نقطة</p>
        </div>
        <div class="sv-hero-stats">
            <div class="sv-stat">
                <strong>{{ $total }}</strong>
                <span>مشرف في السباق</span>
            </div>
            @if($total > 0)
            <div class="sv-stat">
                <strong>{{ $sups[0]['points'] ?? 0 }}</strong>
                <span>أعلى نقاط</span>
            </div>
            @endif
        </div>
    </div>

    @if($total === 0)
        <div class="empty-sv">
            <span class="e-icon">🏜️</span>
            <p>لا يوجد مشرفون في السباق حالياً</p>
        </div>
    @else

    {{-- Podium --}}
    @if(count($top3) > 0)
    <div class="podium-section">
        <div class="podium-title">🏅 منصة التتويج</div>
        <div class="podium-row">
            @foreach($top3 as $sup)
            <div class="podium-card rank-{{ $sup['rank'] }}">
                <span class="podium-medal">{{ $sup['medal'] }}</span>
                <div class="podium-name">{{ $sup['name'] }}</div>
                <div class="podium-pts">{{ number_format($sup['points']) }} نقطة</div>
                <div class="podium-milestone">{{ $sup['milestone'] }}</div>
                @if($sup['is_me'])
                    <div style="margin-top:0.4rem;font-size:0.6rem;color:var(--gold);font-weight:800">أنت هنا ⭐</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Race track --}}
    <div class="track-wrapper">

        {{-- Milestone header --}}
        <div class="milestone-header">
            @foreach($milestones as $m)
                <div class="milestone-tick">{{ $m['label'] }}</div>
            @endforeach
            <div style="width:32px;flex-shrink:0;text-align:center;font-size:0.7rem;">🏁</div>
        </div>

        {{-- All lanes --}}
        <div class="race-lanes">
            @foreach($sups as $index => $sup)
            @php
                $color = $laneColors[$index % count($laneColors)];
                $pct   = $sup['pct'];
            @endphp
            <div class="lane {{ $sup['is_me'] ? 'is-me' : '' }}">

                {{-- Name column --}}
                <div class="lane-name-col">
                    @if($index < 3)
                        <div class="lane-medal">{{ $sup['medal'] }}</div>
                    @else
                        <div class="lane-rank-num">#{{ $sup['rank'] }}</div>
                    @endif

                    <div class="lane-avatar" style="background:{{ $color }}">
                        {{ mb_substr($sup['name'], 0, 1) }}
                    </div>

                    <div class="lane-info">
                        <div class="lane-info-name">{{ $sup['name'] }}</div>
                        <div class="lane-info-role">{{ $sup['role_label'] }}</div>
                        @if($sup['is_me'])
                            <div class="lane-me-badge">أنت</div>
                        @endif
                    </div>
                </div>

                {{-- Track column --}}
                <div class="lane-track-col">

                    {{-- Vertical grid lines --}}
                    <div class="lane-grid">
                        @foreach($milestones as $m)
                            <div class="lane-grid-line"></div>
                        @endforeach
                        <div style="width:32px;flex-shrink:0;"></div>
                    </div>

                    {{-- Road --}}
                    <div class="lane-road">
                        {{-- Finish line --}}
                        <div class="finish-line" style="left:0"></div>

                        {{-- Fill bar (RTL: grows from right) --}}
                        <div class="lane-fill"
                             data-pct="{{ $pct }}"
                             style="
                                 background: linear-gradient(270deg, {{ $color }}dd, {{ $color }}66);
                                 right: {{ 100 - $pct }}%;
                                 width: {{ $pct }}%;
                             ">
                        </div>

                        {{-- Points label --}}
                        <div class="lane-pts-label">{{ $sup['points'] }}ن</div>

                        {{-- Runner --}}
                        @if($pct > 0)
                        <div class="lane-runner"
                             data-pct="{{ $pct }}"
                             style="right: calc({{ 100 - $pct }}% - 14px)">
                            🏃
                        </div>
                        @endif
                    </div>

                    {{-- Meta row --}}
                    <div class="lane-meta">
                        <span class="lane-milestone">{{ $sup['milestone'] }}</span>
                        <span class="lane-pct" style="color:{{ $color }}">{{ $pct }}%</span>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

    </div>
    @endif

</div>

</x-filament-panels::page>