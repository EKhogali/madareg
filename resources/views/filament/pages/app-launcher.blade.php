<x-filament-panels::page>
    @php
        $user = auth()->user();
        $isSupervisor = $user && (int) $user->role === 3;

        /**
         * Build “apps” list.
         * Put all items you currently have in sidebar:
         * - Dashboard
         * - Activities (resource)
         * - Groups (resource)
         * - Subscribers (resource)
         * - Users (resource)
         * - Follow up monthly sheet (page)
         * - Monthly follow up report (page)
         * - Top management report (page)
         *
         * If any route name differs in your project, adjust it here only.
         */
        $apps = [
            [
                'label' => 'لوحة التحكم',
                'desc'  => 'نظرة عامة',
                'route' => route('filament.admin.pages.dashboard'),
                'icon'  => 'heroicon-o-home',
                'bg'    => 'from-slate-700 to-slate-900',
            ],
            [
                'label' => 'الأنشطة',
                'desc'  => 'إدارة الأنشطة',
                'route' => route('filament.admin.resources.activities.index'),
                'icon'  => 'heroicon-o-rectangle-stack',
                'bg'    => 'from-orange-500 to-amber-600',
            ],
            [
                'label' => 'المجموعات',
                'desc'  => 'البيانات الأساسية',
                'route' => route('filament.admin.resources.groups.index'),
                'icon'  => 'heroicon-o-rectangle-group',
                'bg'    => 'from-sky-500 to-blue-600',
            ],
            [
                'label' => 'المشتركين',
                'desc'  => 'البيانات الأساسية',
                'route' => route('filament.admin.resources.subscribers.index'),
                'icon'  => 'heroicon-o-users',
                'bg'    => 'from-emerald-500 to-teal-600',
            ],
            [
                'label' => 'المستخدمون',
                'desc'  => 'إدارة النظام',
                'route' => route('filament.admin.resources.users.index'),
                'icon'  => 'heroicon-o-user-group',
                'bg'    => 'from-violet-500 to-purple-700',
            ],
            [
                'label' => 'المتابعة الشهرية',
                'desc'  => 'نموذج المتابعة',
                'route' => route('filament.admin.pages.follow-up-monthly-sheet'),
                'icon'  => 'heroicon-o-calendar-days',
                'bg'    => 'from-cyan-500 to-sky-600',
            ],
            [
                'label' => 'تقرير المتابعة الشهرية',
                'desc'  => 'تقارير المتابعة',
                'route' => route('filament.admin.pages.monthly-follow-up-report'),
                'icon'  => 'heroicon-o-document-text',
                'bg'    => 'from-fuchsia-500 to-pink-600',
            ],
            [
                'label' => 'تقرير الإدارة العليا',
                'desc'  => 'لوحة مؤشرات',
                'route' => \App\Filament\Pages\TopManagementMonthlyReport::getUrl(),
                'icon'  => 'heroicon-o-chart-bar-square',
                'bg'    => 'from-indigo-600 to-blue-800',
            ],
        ];
    @endphp

    {{-- Header --}}
    <div class="flex flex-col gap-2">

    </div>

    {{-- Odoo-like background container --}}
    <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="p-6 sm:p-8"
             style="
                background:
                radial-gradient(1200px 600px at 80% 0%, rgba(255, 164, 0, .16), transparent 55%),
                radial-gradient(1000px 600px at 20% 10%, rgba(0, 163, 255, .12), transparent 55%),
                radial-gradient(900px 500px at 50% 100%, rgba(120, 60, 255, .10), transparent 60%);
             "
        >
            {{-- Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
                @foreach ($apps as $app)
                    <a href="{{ $app['route'] }}"
                       class="
                        group relative rounded-2xl
                        border border-white/10 dark:border-white/10
                        bg-white/60 dark:bg-gray-950/40
                        backdrop-blur
                        hover:bg-white/80 dark:hover:bg-gray-950/55
                        transition
                        shadow-sm hover:shadow-md
                        p-4 sm:p-5
                        flex flex-col items-center text-center
                       "
                    >
                        {{-- Icon badge --}}
                        <div class="
                            w-14 h-14 sm:w-16 sm:h-16 rounded-2xl
                            bg-gradient-to-br {{ $app['bg'] }}
                            flex items-center justify-center
                            shadow-sm
                            ring-1 ring-white/20
                            group-hover:scale-[1.03]
                            transition
                        ">
                            @svg($app['icon'], 'w-8 h-8 text-white')
                        </div>

                        {{-- Label --}}
                        <div class="mt-3 font-bold text-gray-900 dark:text-white text-sm sm:text-base">
                            {{ $app['label'] }}
                        </div>

                        {{-- Desc --}}
                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                            {{ $app['desc'] }}
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Hint --}}
            <div class="mt-6 text-xs text-gray-500 dark:text-gray-400">
                ملاحظة: هذه الصفحة مصممة لتسهيل الوصول السريع مثل واجهة Odoo.
            </div>
        </div>
    </div>

    {{-- Optional: hide sidebar completely for non-supervisor (role != 3) --}}
    @if (! $isSupervisor)
        <style>
            /* Filament sidebar wrapper selectors vary by version; these work for most v3/v4 layouts */
            aside.fi-sidebar,
            .fi-sidebar,
            [data-testid="sidebar"] {
                display: none !important;
            }
            /* Expand content full width */
            main.fi-main,
            .fi-main {
                margin-inline-start: 0 !important;
            }
        </style>
    @endif
</x-filament-panels::page>
