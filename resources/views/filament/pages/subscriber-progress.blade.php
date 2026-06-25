<x-filament-panels::page>

{{-- ══════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════ --}}
<style>
    /* ── Palette ── */
    :root {
        --mn-gold:   #F4A623;
        --mn-blue:   #0076BF;
        --mn-navy:   #1A2B4A;
        --mn-sky:    #E8F4FD;
        --mn-white:  #FFFFFF;
        --stage-1:   #3B82F6;
        --stage-2:   #8B5CF6;
        --stage-3:   #F59E0B;
        --stage-4:   #EF4444;
        --stage-5:   #10B981;
    }

    .progress-page {
        font-family: 'Tajawal', 'Cairo', sans-serif;
        direction: rtl;
        padding: 0.5rem 0 2rem;
    }

    /* ── Hero banner ── */
    .progress-hero {
        background: linear-gradient(135deg, var(--mn-navy) 0%, #0076BF 60%, #00A8E8 100%);
        border-radius: 1.5rem;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,118,191,0.25);
    }
    .progress-hero::before {
        content: '';
        position: absolute;
        top: -40px; left: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .progress-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; right: -30px;
        width: 260px; height: 260px;
        background: rgba(244,166,35,0.12);
        border-radius: 50%;
    }
    .hero-stars {
        font-size: 3.5rem;
        animation: pulse-star 2s ease-in-out infinite;
        z-index: 1;
    }
    @keyframes pulse-star {
        0%, 100% { transform: scale(1) rotate(0deg); }
        50%       { transform: scale(1.1) rotate(8deg); }
    }
    .hero-text { z-index: 1; flex: 1; }
    .hero-text h1 {
        color: var(--mn-gold);
        font-size: 1.9rem;
        font-weight: 800;
        margin: 0 0 0.25rem;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .hero-text p {
        color: rgba(255,255,255,0.8);
        font-size: 1rem;
        margin: 0;
    }
    .hero-total {
        z-index: 1;
        text-align: center;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        padding: 0.75rem 1.25rem;
        backdrop-filter: blur(8px);
    }
    .hero-total .big-num {
        color: var(--mn-gold);
        font-size: 2rem;
        font-weight: 900;
        display: block;
        line-height: 1;
    }
    .hero-total .small-label {
        color: rgba(255,255,255,0.7);
        font-size: 0.75rem;
    }

    /* ── Stage legend bar ── */
    .stage-legend {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
    }
    .stage-pill {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* ── Filter bar ── */
    .filter-bar {
        background: white;
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .filter-bar label {
        font-weight: 700;
        color: var(--mn-navy);
        font-size: 0.9rem;
        white-space: nowrap;
    }
    .filter-bar select {
        flex: 1;
        min-width: 180px;
        border: 1.5px solid #E2E8F0;
        border-radius: 0.5rem;
        padding: 0.45rem 0.75rem;
        font-size: 0.9rem;
        color: var(--mn-navy);
        background: #F8FAFC;
        outline: none;
        transition: border-color 0.2s;
        font-family: inherit;
        direction: rtl;
    }
    .filter-bar select:focus { border-color: var(--mn-blue); }

    /* ── Cards grid ── */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    /* ── Progress card ── */
    .progress-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        opacity: 0;
        transform: translateY(20px);
        animation: card-in 0.5s ease forwards;
    }
    .progress-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.14);
    }
    @keyframes card-in {
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Card top colored band ── */
    .card-band {
        height: 6px;
        width: 100%;
    }

    /* ── Card body ── */
    .card-body {
        padding: 1.25rem 1.25rem 1rem;
    }

    /* ── Avatar + name row ── */
    .card-header-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .avatar {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .avatar-placeholder {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .avatar-stage-badge {
        position: absolute;
        bottom: -2px;
        left: -2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        border: 2px solid white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }
    .card-name-group { flex: 1; min-width: 0; }
    .card-name {
        font-weight: 800;
        font-size: 1.05rem;
        color: var(--mn-navy);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.15rem;
    }
    .card-group {
        font-size: 0.78rem;
        color: #94A3B8;
    }
    .card-emoji {
        font-size: 2rem;
        line-height: 1;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        animation: emoji-float 3s ease-in-out infinite;
    }
    @keyframes emoji-float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-5px); }
    }

    /* ── Points display ── */
    .points-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 0.6rem;
    }
    .points-left .points-num {
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
        color: var(--mn-navy);
    }
    .points-left .points-label {
        font-size: 0.72rem;
        color: #94A3B8;
        display: block;
    }
    .points-right {
        text-align: left;
    }
    .milestone-badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.2rem;
    }
    .pct-label {
        font-size: 0.72rem;
        color: #94A3B8;
        display: block;
        text-align: left;
    }

    /* ── Overall progress bar ── */
    .overall-bar-wrap {
        background: #F1F5F9;
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 0.4rem;
        position: relative;
    }
    .overall-bar-fill {
        height: 100%;
        border-radius: 999px;
        position: relative;
        transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        width: 0%;
    }
    .overall-bar-fill::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0%   { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* ── Stage mini bars ── */
    .stage-mini-bars {
        display: flex;
        gap: 3px;
        margin-top: 0.6rem;
    }
    .stage-mini-bar-wrap {
        flex: 1;
        position: relative;
    }
    .stage-mini-label {
        font-size: 0.6rem;
        color: #CBD5E1;
        text-align: center;
        display: block;
        margin-bottom: 2px;
        font-weight: 600;
    }
    .stage-mini-bar-bg {
        background: #F1F5F9;
        border-radius: 999px;
        height: 5px;
        overflow: hidden;
    }
    .stage-mini-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 1.4s cubic-bezier(0.4, 0, 0.2, 1) 0.3s;
        width: 0%;
    }

    /* ── Footer of card ── */
    .card-footer {
        padding: 0.7rem 1.25rem;
        background: #F8FAFC;
        border-top: 1px solid #F1F5F9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .next-stage-hint {
        font-size: 0.75rem;
        color: #64748B;
    }
    .next-stage-hint strong {
        color: var(--mn-navy);
    }
    .stage-name-chip {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.2rem 0.65rem;
        border-radius: 2rem;
        color: white;
    }

    /* ── Journey track (bottom of card) ── */
    .journey-track {
        padding: 0.5rem 1.25rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0;
        position: relative;
    }
    .journey-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    .journey-dot.completed {
        box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
    }
    .journey-dot.current {
        width: 18px;
        height: 18px;
        box-shadow: 0 0 0 3px white, 0 0 0 5px currentColor;
        animation: current-pulse 1.5s ease-in-out infinite;
    }
    @keyframes current-pulse {
        0%, 100% { box-shadow: 0 0 0 3px white, 0 0 0 5px currentColor; }
        50%       { box-shadow: 0 0 0 3px white, 0 0 12px currentColor; }
    }
    .journey-line {
        flex: 1;
        height: 3px;
        position: relative;
        overflow: hidden;
    }
    .journey-line-bg { height: 100%; background: #E2E8F0; }
    .journey-line-fill {
        position: absolute;
        top: 0; left: 0;
        height: 100%;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1) 0.5s;
        width: 0%;
    }
    .journey-walker {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        transition: left 1.5s cubic-bezier(0.4, 0, 0.2, 1) 0.5s;
        animation: walk 0.4s steps(2) infinite;
        left: 0%;
    }
    @keyframes walk {
        0%   { transform: translateY(-50%) scaleX(1); }
        50%  { transform: translateY(-55%) scaleX(1); }
        100% { transform: translateY(-50%) scaleX(1); }
    }
    .journey-flag {
        font-size: 1rem;
        flex-shrink: 0;
        z-index: 1;
    }
    .journey-dot-label {
        position: absolute;
        bottom: -14px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.55rem;
        color: #94A3B8;
        white-space: nowrap;
        font-weight: 600;
    }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #94A3B8;
    }
    .empty-state .empty-icon { font-size: 4rem; margin-bottom: 1rem; }
    .empty-state p { font-size: 1rem; }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .progress-hero { flex-direction: column; text-align: center; }
        .hero-total { width: 100%; }
        .cards-grid { grid-template-columns: 1fr; }
        .hero-text h1 { font-size: 1.4rem; }
    }
</style>

<div class="progress-page" x-data="progressPage()" x-init="init()">

    {{-- ── Hero Banner ── --}}
    <div class="progress-hero">
        <div class="hero-stars">🌟</div>
        <div class="hero-text">
            <h1>مدارج النور — مسار التقدم</h1>
            <p>رحلة من 1000 خطوة نحو النور</p>
        </div>
        @if(count($this->progressData) > 0)
        <div class="hero-total">
            <span class="big-num">{{ count($this->progressData) }}</span>
            <span class="small-label">مشترك</span>
        </div>
        @endif
    </div>

    {{-- ── Stage Legend ── --}}
    <div class="stage-legend">
        <div class="stage-pill" style="background: var(--stage-1)">✨ بصيص &nbsp;<small style="opacity:0.8">1–200</small></div>
        <div class="stage-pill" style="background: var(--stage-2)">⚡ بريق &nbsp;<small style="opacity:0.8">201–400</small></div>
        <div class="stage-pill" style="background: var(--stage-3)">🌟 ضياء &nbsp;<small style="opacity:0.8">401–600</small></div>
        <div class="stage-pill" style="background: var(--stage-4)">🔥 وميض &nbsp;<small style="opacity:0.8">601–800</small></div>
        <div class="stage-pill" style="background: var(--stage-5)">☀️ نور &nbsp;<small style="opacity:0.8">801–1000</small></div>
    </div>

    {{-- ── Filter Bar ── --}}
    @php
        $options = $this->subscriberOptions();
        $role    = (int) auth()->user()?->role;
    @endphp
    @if($role !== 5 && count($options) > 1)
    <div class="filter-bar">
        <label>🔍 تصفية حسب المشترك:</label>
        <select wire:model.live="filterSubscriberId">
            <option value="">— جميع المشتركين —</option>
            @foreach($options as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- ── Cards Grid ── --}}
    @if(count($this->progressData) === 0)
        <div class="empty-state">
            <div class="empty-icon">🔭</div>
            <p>لا يوجد مشتركون في نطاقك حالياً</p>
        </div>
    @else
    <div class="cards-grid">
        @foreach($this->progressData as $index => $sub)
        @php
            $color   = $sub['stage']['color'];
            $emoji   = $sub['stage']['emoji'];
            $stageName = $sub['stage']['name'];
            $initials = mb_substr($sub['name'], 0, 1);

            // Per-stage progress: full=100 if completed, partial if current, 0 if not reached
            $stageColors = ['#3B82F6','#8B5CF6','#F59E0B','#EF4444','#10B981'];
            $stageMaxes  = [200, 400, 600, 800, 1000];
            $stageMins   = [1,   201, 401, 601, 801];
            $stageNames  = ['بصيص','بريق','ضياء','وميض','نور'];

            $stagePcts = [];
            foreach ($stageMaxes as $si => $smax) {
                $smin = $stageMins[$si];
                $pts  = $sub['points'];
                if ($pts >= $smax) {
                    $stagePcts[] = 100;
                } elseif ($pts >= $smin) {
                    $stagePcts[] = round((($pts - $smin + 1) / ($smax - $smin + 1)) * 100, 1);
                } else {
                    $stagePcts[] = 0;
                }
            }

            // Journey: which segment filled
            $journeyPct = $sub['pct'];
            $delayMs = 100 + ($index * 80);
        @endphp

        <div class="progress-card"
             style="animation-delay: {{ $delayMs }}ms"
             x-data="{ mounted: false }"
             x-init="setTimeout(() => mounted = true, {{ $delayMs }})">

            {{-- colored top band --}}
            <div class="card-band" style="background: linear-gradient(90deg, {{ $color }}, {{ $color }}cc)"></div>

            <div class="card-body">

                {{-- Avatar + Name --}}
                <div class="card-header-row">
                    <div class="avatar-wrap">
                        @if($sub['image'])
                            <img src="{{ Storage::url($sub['image']) }}"
                                 alt="{{ $sub['name'] }}"
                                 class="avatar"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="avatar-placeholder" style="background: {{ $color }}; display:none">
                                {{ $initials }}
                            </div>
                        @else
                            <div class="avatar-placeholder" style="background: {{ $color }}">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="avatar-stage-badge" style="background: {{ $color }}; color:white; font-size:0.65rem">
                            {{ $emoji }}
                        </div>
                    </div>

                    <div class="card-name-group">
                        <div class="card-name">{{ $sub['name'] }}</div>
                        <div class="card-group">{{ $sub['group'] }}</div>
                    </div>

                    <div class="card-emoji">{{ $emoji }}</div>
                </div>

                {{-- Points + milestone --}}
                <div class="points-row">
                    <div class="points-left">
                        <span class="points-num" style="color: {{ $color }}">{{ number_format($sub['points']) }}</span>
                        <span class="points-label">نقطة من 1000</span>
                    </div>
                    <div class="points-right">
                        <span class="milestone-badge" style="background: {{ $color }}">
                            {{ $sub['milestone_title'] }}
                        </span>
                        <span class="pct-label">{{ $sub['pct'] }}% مكتمل</span>
                    </div>
                </div>

                {{-- Overall progress bar --}}
                <div class="overall-bar-wrap">
                    <div class="overall-bar-fill"
                         style="background: linear-gradient(90deg, {{ $color }}99, {{ $color }})"
                         x-bind:style="mounted ? 'width: {{ $sub['pct'] }}%; background: linear-gradient(90deg, {{ $color }}99, {{ $color }})' : 'width:0%'">
                    </div>
                </div>

                {{-- Stage mini bars --}}
                <div class="stage-mini-bars">
                    @foreach($stagePcts as $si => $spct)
                    <div class="stage-mini-bar-wrap">
                        <span class="stage-mini-label">{{ $stageNames[$si] }}</span>
                        <div class="stage-mini-bar-bg">
                            <div class="stage-mini-bar-fill"
                                 style="background: {{ $stageColors[$si] }}"
                                 x-bind:style="mounted ? 'width: {{ $spct }}%' : 'width:0%'">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            {{-- Journey walk track --}}
            <div class="journey-track">
                <div class="journey-dot completed" style="color: {{ $color }}; background: {{ $color }}"></div>

                <div class="journey-line" style="position:relative">
                    <div class="journey-line-bg"></div>
                    <div class="journey-line-fill"
                         style="background: linear-gradient(90deg, {{ $color }}88, {{ $color }})"
                         x-bind:style="mounted ? 'width: {{ $sub['pct'] }}%' : 'width:0%'">
                    </div>
                    {{-- walker character --}}
                    <div class="journey-walker"
                         x-bind:style="mounted ? 'left: calc({{ $sub['pct'] }}% - 10px)' : 'left: 0%'">
                        🏃
                    </div>
                </div>

                <div class="journey-flag">🏁</div>
            </div>

            {{-- Card Footer --}}
            <div class="card-footer">
                <div class="next-stage-hint">
                    @if($sub['points'] >= 1000)
                        🏆 <strong>أتمّ الرحلة!</strong>
                    @elseif($sub['next_stage_pts'])
                        <strong>{{ $sub['next_stage_pts'] }}</strong> نقطة للمرحلة التالية
                    @endif
                </div>
                <span class="stage-name-chip" style="background: {{ $color }}">
                    {{ $stageName }}
                </span>
            </div>

        </div>
        @endforeach
    </div>
    @endif

</div>

<script>
function progressPage() {
    return {
        init() {
            // Trigger bar animations after cards render
            setTimeout(() => {
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el.__x) el.__x.$data.mounted = true;
                });
            }, 200);
        }
    }
}
</script>

</x-filament-panels::page>