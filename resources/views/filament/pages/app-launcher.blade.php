<x-filament-panels::page>
    @php
        $u = auth()->user();
        $role = (int) $u?->role;

        $tiles = [
            [
                'title' => 'مضمار المدارج',
                'subtitle' => 'مضمار الـ 1000 متر',
                'emoji' => '🏆',
                'gradient' => 'from-blue-700 to-blue-900',
                'glow' => 'rgba(6,182,212,0.3)',
                'url' => \App\Filament\Pages\MadmarTrack::getUrl(),
                // 'can'      => true,
                'can' => $u?->isSuperAdmin(),
                'featured' => true,
            ],
            [
                'title' => 'بطاقات المضمار',
                'subtitle' => 'رحلة 1000 نقطة نحو النور',
                'emoji' => '🌟',
                'gradient' => 'from-amber-400 to-yellow-500',
                'glow' => 'rgba(251,191,36,0.35)',
                'url' => \App\Filament\Pages\SubscriberProgress::getUrl(),
                // 'can'      => true,
                'can' => $u?->isSuperAdmin(),
                'special' => true,
            ],
            [
                'title' => 'مضمار المشرفين',
                'subtitle' => 'سباق الإنجاز نحو الـ 1000 نقطة',
                'emoji' => '🏆',
                'gradient' => 'from-amber-500 to-yellow-600',
                'glow' => 'rgba(244,166,35,0.35)',
                'url' => \App\Filament\Pages\SupervisorProgress::getUrl(),
                'can' => $u?->isSuperAdmin(),
            ],
            [
                'title' => 'المتابعة الشهرية',
                'subtitle' => 'إدخال بيانات المتابعة',
                'emoji' => '📋',
                'gradient' => 'from-emerald-500 to-teal-600',
                'glow' => 'rgba(16,185,129,0.3)',
                'url' => \App\Filament\Pages\FollowUpMonthlySheet::getUrl(),
                'can' => in_array($role, [1, 2, 3, 4, 5]),
            ],
            [
                'title' => 'تقارير المتابعة',
                'subtitle' => 'عرض وتحليل النتائج',
                'emoji' => '📊',
                'gradient' => 'from-blue-500 to-indigo-600',
                'glow' => 'rgba(99,102,241,0.3)',
                'url' => \App\Filament\Pages\MonthlyFollowUpReport::getUrl(),
                'can' => in_array($role, [1, 2, 3, 4, 5]),
            ],
            [
                'title' => 'المشتركين',
                'subtitle' => 'إدارة بيانات المشتركين',
                'emoji' => '👥',
                'gradient' => 'from-sky-500 to-blue-600',
                'glow' => 'rgba(14,165,233,0.3)',
                'url' => \App\Filament\Resources\SubscriberResource::getUrl(),
                'can' => $u?->isStaff(),
            ],
            [
                'title' => 'أولياء الأمور',
                'subtitle' => 'بيانات التواصل والعائلة',
                'emoji' => '👨‍👩‍👧',
                'gradient' => 'from-violet-500 to-purple-600',
                'glow' => 'rgba(139,92,246,0.3)',
                'url' => \App\Filament\Resources\ParentResource::getUrl(),
                'can' => $u?->isStaff(),
            ],
            [
                'title' => 'الأنشطة',
                'subtitle' => 'البرامج والفعاليات',
                'emoji' => '✨',
                'gradient' => 'from-rose-500 to-pink-600',
                'glow' => 'rgba(244,63,94,0.3)',
                'url' => \App\Filament\Resources\ActivityResource::getUrl(),
                'can' => $u?->isSuperAdmin() || $u?->isAdmin(),
            ],
            [
                'title' => 'المجموعات',
                'subtitle' => 'تنظيم وتوزيع الفئات',
                'emoji' => '🗂️',
                'gradient' => 'from-slate-500 to-gray-600',
                'glow' => 'rgba(100,116,139,0.3)',
                'url' => \App\Filament\Resources\GroupResource::getUrl(),
                'can' => $u?->isSuperAdmin(),
            ],
            [
                'title' => 'المستخدمون',
                'subtitle' => 'إدارة صلاحيات النظام',
                'emoji' => '🛡️',
                'gradient' => 'from-orange-500 to-amber-600',
                'glow' => 'rgba(249,115,22,0.3)',
                'url' => \App\Filament\Resources\UserResource::getUrl(),
                'can' => $u?->isSuperAdmin(),
            ],
            [
                'title' => 'الإدارة العليا',
                'subtitle' => 'لوحة مؤشرات الأداء',
                'emoji' => '📈',
                'gradient' => 'from-cyan-500 to-blue-500',
                'glow' => 'rgba(6,182,212,0.3)',
                'url' => \App\Filament\Pages\TopManagementMonthlyReport::getUrl(),
                'can' => $u?->isSuperAdmin(),
            ],
        ];

        $visibleTiles = array_filter($tiles, fn($t) => $t['can'] ?? false);
    @endphp

    <style>
        /* ── Reset & base ── */
        .launcher-page {
            font-family: 'Tajawal', 'Cairo', ui-sans-serif, sans-serif;
            direction: rtl;
            min-height: 80vh;
            padding: 0 0 3rem;
        }

        /* ── Top hero ── */
        .launcher-hero {
            position: relative;
            border-radius: 1.75rem;
            overflow: hidden;
            margin-bottom: 2.5rem;
            padding: 2.25rem 2.5rem;
            background: linear-gradient(135deg, #0A1628 0%, #0076BF 55%, #00A8E8 100%);
            display: flex;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 12px 40px rgba(0, 118, 191, 0.3);
        }

        /* decorative circles */
        .launcher-hero::before,
        .launcher-hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .launcher-hero::before {
            width: 320px;
            height: 320px;
            top: -100px;
            right: -80px;
            background: radial-gradient(circle, rgba(244, 166, 35, 0.15) 0%, transparent 70%);
        }

        .launcher-hero::after {
            width: 200px;
            height: 200px;
            bottom: -60px;
            left: 20px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, transparent 70%);
        }

        .hero-logo {
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }

        .hero-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
            animation: logo-breathe 4s ease-in-out infinite;
        }

        @keyframes logo-breathe {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .hero-content {
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .hero-greeting {
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .hero-name {
            font-size: 1.9rem;
            font-weight: 900;
            color: #F4A623;
            line-height: 1.15;
            margin-bottom: 0.35rem;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);
        }

        .hero-sub {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-badge {
            position: relative;
            z-index: 2;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 0.85rem 1.4rem;
            text-align: center;
        }

        .hero-badge-num {
            display: block;
            font-size: 2.2rem;
            font-weight: 900;
            color: #F4A623;
            line-height: 1;
        }

        .hero-badge-label {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.6);
            display: block;
            margin-top: 0.15rem;
        }

        /* ── Star divider ── */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            color: #94A3B8;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.06em;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E2E8F0, transparent);
        }

        /* ── Tiles grid ── */
        .tiles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.1rem;
        }

        /* ── Single tile ── */
        .launcher-tile {
            position: relative;
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.75rem 1rem 1.5rem;
            gap: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            border: 1.5px solid #F1F5F9;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            opacity: 0;
            transform: translateY(18px);
            animation: tile-in 0.45s ease forwards;
        }

        .launcher-tile:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 16px 40px var(--tile-glow, rgba(0, 0, 0, 0.12));
            border-color: transparent;
        }

        /* top gradient bar */
        .launcher-tile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--tile-gradient);
            opacity: 0;
            transition: opacity 0.22s ease;
        }

        .launcher-tile:hover::before {
            opacity: 1;
        }

        /* glow background on hover */
        .launcher-tile::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--tile-gradient);
            opacity: 0;
            transition: opacity 0.22s ease;
            border-radius: inherit;
        }

        .launcher-tile:hover::after {
            opacity: 0.04;
        }

        @keyframes tile-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Featured tile ── */
        .launcher-tile.featured {
            background: linear-gradient(135deg, #1A2B4A 0%, #0076BF 100%);
            border-color: transparent;
            box-shadow: 0 8px 28px rgba(0, 118, 191, 0.25);
            grid-column: span 2;
            flex-direction: row;
            padding: 1.5rem 2rem;
            gap: 1.25rem;
            justify-content: flex-start;
        }

        .launcher-tile.featured:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 118, 191, 0.4);
        }

        .launcher-tile.featured::after {
            display: none;
        }

        .launcher-tile.featured::before {
            display: none;
        }

        .launcher-tile.featured .tile-emoji {
            font-size: 2.8rem;
            animation: star-spin 6s linear infinite;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        @keyframes star-spin {
            0% {
                transform: rotate(0deg) scale(1);
            }

            25% {
                transform: rotate(8deg) scale(1.05);
            }

            50% {
                transform: rotate(0deg) scale(1);
            }

            75% {
                transform: rotate(-8deg) scale(1.05);
            }

            100% {
                transform: rotate(0deg) scale(1);
            }
        }

        .launcher-tile.featured .tile-text {
            text-align: right;
        }

        .launcher-tile.featured .tile-title {
            font-size: 1.2rem;
            color: #F4A623;
            margin-bottom: 0.2rem;
        }

        .launcher-tile.featured .tile-subtitle {
            color: rgba(255, 255, 255, 0.65);
        }

        .launcher-tile.featured .featured-arrow {
            margin-right: auto;
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.3rem;
            transition: transform 0.2s ease, color 0.2s ease;
            flex-shrink: 0;
        }

        .launcher-tile.featured:hover .featured-arrow {
            transform: translateX(-4px);
            color: #F4A623;
        }

        /* ── Emoji icon ── */
        .tile-emoji {
            font-size: 2.2rem;
            margin-bottom: 0.85rem;
            line-height: 1;
            display: block;
            transition: transform 0.2s ease;
        }

        .launcher-tile:not(.featured):hover .tile-emoji {
            transform: scale(1.2) translateY(-2px);
        }

        /* ── Text ── */
        .tile-text {
            text-align: center;
        }

        .tile-title {
            font-size: 1rem;
            font-weight: 800;
            color: #1E293B;
            display: block;
            margin-bottom: 0.2rem;
        }

        .tile-subtitle {
            font-size: 0.74rem;
            color: #94A3B8;
            display: block;
        }

        /* ── Enter hint ── */
        .tile-enter {
            position: absolute;
            bottom: 0.75rem;
            font-size: 0.68rem;
            color: #CBD5E1;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .launcher-tile:not(.featured):hover .tile-enter {
            opacity: 1;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .launcher-hero {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
                gap: 1rem;
            }

            .hero-badge {
                width: 100%;
            }

            .tiles-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .launcher-tile.featured {
                grid-column: span 2;
                flex-direction: column;
                text-align: center;
                padding: 1.25rem 1rem;
            }

            .launcher-tile.featured .featured-arrow {
                margin: 0 auto;
            }

            .hero-name {
                font-size: 1.4rem;
            }
        }

        /* ── Special tile (بطاقات المضمار) ── */
        .launcher-tile.special {
            background: linear-gradient(160deg, #FEF9EC 0%, #FEF3C7 60%, #FDE68A 100%);
            border: 2px solid #F4A623;
            box-shadow: 0 8px 28px rgba(251, 191, 36, 0.25);
            flex-direction: column;
            padding: 1.75rem 1.25rem;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            min-height: 140px;
        }

        .launcher-tile.special:hover {
            transform: none !important;
            box-shadow: 0 16px 40px rgba(251, 191, 36, 0.45) !important;
            border-color: #D97706;
            animation: tile-shake 0.5s ease-in-out infinite !important;
        }

        @keyframes tile-shake {

            0%,
            100% {
                transform: translateY(-3px) rotate(0deg);
            }

            20% {
                transform: translateY(-3px) rotate(-2.5deg);
            }

            40% {
                transform: translateY(-4px) rotate(2.5deg);
            }

            60% {
                transform: translateY(-3px) rotate(-1.5deg);
            }

            80% {
                transform: translateY(-4px) rotate(1.5deg);
            }
        }

        .launcher-tile.special::before {
            display: none;
        }

        .launcher-tile.special::after {
            display: none;
        }

        /* Pulsing star icon */
        .launcher-tile.special .tile-emoji {
            font-size: 2.6rem;
            margin-bottom: 0.5rem;
            display: block;
            animation: special-pulse 2s ease-in-out infinite;
            filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.8));
        }

        @keyframes special-pulse {

            0%,
            100% {
                transform: scale(1);
                filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.6));
            }

            50% {
                transform: scale(1.15);
                filter: drop-shadow(0 0 14px rgba(251, 191, 36, 1));
            }
        }

        .launcher-tile.special .tile-title {
            font-size: 1.05rem;
            color: #92400E;
            font-weight: 900;
        }

        .launcher-tile.special .tile-subtitle {
            color: #B45309;
            font-size: 0.72rem;
        }

        /* Decorative top shimmer line */
        .launcher-tile.special::before {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #F4A623, transparent);
            animation: shimmer-line 2.5s ease-in-out infinite;
        }

        @keyframes shimmer-line {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 1;
            }
        }

        /* dark mode */
        .dark .launcher-tile {
            background: #1E293B;
            border-color: #334155;
        }

        .dark .tile-title {
            color: #F1F5F9;
        }

        .dark .launcher-tile.featured {
            background: linear-gradient(135deg, #0F172A 0%, #0C4A6E 100%);
        }
    </style>

    <div class="launcher-page">

        {{-- ── Hero ── --}}
        <div class="launcher-hero">
            <div class="hero-logo">
                <img src="{{ asset('images/madarej-alnoor.jpg') }}" alt="مدارج النور"
                    onerror="this.style.display='none'">
            </div>

            <div class="hero-content">
                <p class="hero-greeting">مرحباً بك في نظام</p>
                <h1 class="hero-name">{{ $u?->name }}</h1>
                <p class="hero-sub">مدارج النور — نحو جيل مضيء 🌙</p>
            </div>

            <div class="hero-badge">
                <span class="hero-badge-num">{{ count($visibleTiles) }}</span>
                <span class="hero-badge-label">تطبيق متاح</span>
            </div>
        </div>

        {{-- ── Divider ── --}}
        <div class="section-divider">اختر وجهتك</div>

        {{-- ── Tiles ── --}}
        <div class="tiles-grid">
            @foreach($visibleTiles as $index => $tile)
                    @php
                        $delayMs = 60 + ($index * 55);
                        $isFeatured = $tile['featured'] ?? false;
                    @endphp
                    @php
                        $isSpecial = $tile['special'] ?? false;
                        $tileClass = $isFeatured ? 'featured' : ($isSpecial ? 'special' : '');
                        $tileGrad = $isFeatured
                            ? 'linear-gradient(135deg,#0076BF,#00A8E8)'
                            : "linear-gradient(135deg,{$tile['gradient']})";
                    @endphp
                    <a href="{{ $tile['url'] }}" class="launcher-tile {{ $tileClass }}" style="
                       --tile-gradient: {{ $tileGrad }};
                       --tile-glow: {{ $tile['glow'] }};
                       animation-delay: {{ $delayMs }}ms;
                   ">

                        @if($isFeatured)
                            <div class="tile-emoji">{{ $tile['emoji'] }}</div>
                            <div class="tile-text">
                                <span class="tile-title">{{ $tile['title'] }}</span>
                                <span class="tile-subtitle">{{ $tile['subtitle'] }}</span>
                            </div>
                            <div class="featured-arrow">←</div>
                        @elseif($isSpecial)
                            <span class="tile-emoji">{{ $tile['emoji'] }}</span>
                            <div class="tile-text">
                                <span class="tile-title">{{ $tile['title'] }}</span>
                                <span class="tile-subtitle">{{ $tile['subtitle'] }}</span>
                            </div>
                        @else
                            <span class="tile-emoji">{{ $tile['emoji'] }}</span>
                            <div class="tile-text">
                                <span class="tile-title">{{ $tile['title'] }}</span>
                                <span class="tile-subtitle">{{ $tile['subtitle'] }}</span>
                            </div>
                            <span class="tile-enter">اضغط للدخول</span>
                        @endif
                    </a>
            @endforeach
        </div>

    </div>

</x-filament-panels::page>