<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl">
        {{-- Welcome Header --}}
        <div class="mb-10 flex flex-col items-center text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-4xl">
                أهلاً بك، {{ auth()->user()->name }} 👋
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                اختر الوجهة التي ترغب في الانتقال إليها اليوم
            </p>
        </div>

        @php
            $u = auth()->user();

            $tiles = [
                [
                    'title' => 'المشتركين',
                    'subtitle' => 'إدارة بيانات المشتركين',
                    'icon' => 'heroicon-o-user-group',
                    'color' => 'primary',
                    'url' => \App\Filament\Resources\SubscriberResource::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'أولياء الأمور',
                    'subtitle' => 'بيانات التواصل والعائلة',
                    'icon' => 'heroicon-o-identification',
                    'color' => 'info',
                    'url' => \App\Filament\Resources\ParentResource::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'المتابعة الشهرية',
                    'subtitle' => 'إدخال بيانات المتابعة',
                    'icon' => 'heroicon-o-clipboard-document-check',
                    'color' => 'success',
                    'url' => \App\Filament\Pages\FollowUpMonthlySheet::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'تقارير المتابعة',
                    'subtitle' => 'عرض وتحليل النتائج',
                    'icon' => 'heroicon-o-chart-bar-square',
                    'color' => 'warning',
                    'url' => \App\Filament\Pages\MonthlyFollowUpReport::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'الأنشطة',
                    'subtitle' => 'البرامج والفعاليات',
                    'icon' => 'heroicon-o-sparkles',
                    'color' => 'danger',
                    'url' => \App\Filament\Resources\ActivityResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'المجموعات',
                    'subtitle' => 'تنظيم وتوزيع الفئات',
                    'icon' => 'heroicon-o-rectangle-group',
                    'color' => 'gray',
                    'url' => \App\Filament\Resources\GroupResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'المستخدمون',
                    'subtitle' => 'إدارة صلاحيات النظام',
                    'icon' => 'heroicon-o-shield-check',
                    'color' => 'primary',
                    'url' => \App\Filament\Resources\UserResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'الإدارة العليا',
                    'subtitle' => 'لوحة مؤشرات الأداء',
                    'icon' => 'heroicon-o-presentation-chart-line',
                    'color' => 'success',
                    'url' => \App\Filament\Pages\TopManagementMonthlyReport::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($tiles as $tile)
                @continue(!($tile['can'] ?? false))

                <a href="{{ $tile['url'] }}"
                   class="group relative flex flex-col items-center justify-center overflow-hidden rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-950/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-primary-500/50 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-primary-400/50">
                    
                    {{-- Decorative Background Glow --}}
                    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-{{ $tile['color'] }}-500/5 transition-all group-hover:bg-{{ $tile['color'] }}-500/10"></div>

                    {{-- Icon Container --}}
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-{{ $tile['color'] }}-50 text-{{ $tile['color'] }}-600 ring-1 ring-{{ $tile['color'] }}-200 transition-colors group-hover:bg-{{ $tile['color'] }}-600 group-hover:text-white dark:bg-{{ $tile['color'] }}-500/10 dark:ring-{{ $tile['color'] }}-400/20">
                        @svg($tile['icon'], 'h-8 w-8')
                    </div>

                    {{-- Text --}}
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $tile['title'] }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $tile['subtitle'] }}
                        </p>
                    </div>

                    {{-- Subtle Arrow Indicator --}}
                    <div class="mt-4 flex translate-y-4 opacity-0 transition-all group-hover:translate-y-0 group-hover:opacity-100">
                        <span class="text-{{ $tile['color'] }}-600 dark:text-{{ $tile['color'] }}-400 text-sm font-medium">دخول الآن ←</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <style>
        /* Essential styles for dynamic color classes if not purged by Tailwind */
        .bg-primary-50 { background-color: rgba(var(--primary-50), 1); }
        .text-primary-600 { color: rgba(var(--primary-600), 1); }
        .bg-primary-600 { background-color: rgba(var(--primary-600), 1); }
    </style>
</x-filament-panels::page>