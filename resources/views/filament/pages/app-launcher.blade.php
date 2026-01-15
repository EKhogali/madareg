{{-- resources/views/filament/pages/app-launcher.blade.php --}}

<x-filament-panels::page>
    @php
        // Brand colors (from your logo)
        $brandBlue = '#053688';
        $brandCyan = '#09B7DB';
        $brandGold = '#D6A13A';

        // Safer dashboard URL (works even if route name differs)
        $dashboardUrl = url('/admin');

        $apps = [
            [
                'label' => 'لوحة التحكم',
                'desc'  => 'نظرة عامة',
                'url'   => $dashboardUrl,
                'icon'  => 'dashboard', // maps to x-app-icons.dashboard
                'bg'    => "linear-gradient(135deg, {$brandBlue}, {$brandCyan})",
            ],
            [
                'label' => 'الأنشطة',
                'desc'  => 'إدارة الأنشطة',
                'url'   => route('filament.admin.resources.activities.index'),
                'icon'  => 'activities',
                'bg'    => "linear-gradient(135deg, {$brandCyan}, {$brandBlue})",
            ],
            [
                'label' => 'المجموعات',
                'desc'  => 'البيانات الأساسية',
                'url'   => route('filament.admin.resources.groups.index'),
                'icon'  => 'groups',
                'bg'    => "linear-gradient(135deg, {$brandBlue}, #0f172a)",
            ],
            [
                'label' => 'المشتركين',
                'desc'  => 'البيانات الأساسية',
                'url'   => route('filament.admin.resources.subscribers.index'),
                'icon'  => 'subscribers',
                'bg'    => "linear-gradient(135deg, {$brandGold}, #f59e0b)",
            ],
            [
                'label' => 'المستخدمون',
                'desc'  => 'إدارة النظام',
                'url'   => route('filament.admin.resources.users.index'),
                'icon'  => 'users',
                'bg'    => "linear-gradient(135deg, #475569, #0f172a)",
            ],
            [
                'label' => 'المتابعة الشهرية',
                'desc'  => 'نموذج المتابعة',
                'url'   => route('filament.admin.pages.follow-up-monthly-sheet'),
                'icon'  => 'monthly_followup',
                'bg'    => "linear-gradient(135deg, {$brandGold}, #b45309)",
            ],
            [
                'label' => 'تقرير المتابعة الشهرية',
                'desc'  => 'تقارير المتابعة',
                'url'   => route('filament.admin.pages.monthly-follow-up-report'),
                'icon'  => 'monthly_report',
                'bg'    => "linear-gradient(135deg, #ec4899, #7c3aed)",
            ],
            [
                'label' => 'تقرير الإدارة العليا',
                'desc'  => 'لوحة مؤشرات',
                'url'   => \App\Filament\Pages\TopManagementMonthlyReport::getUrl(),
                'icon'  => 'management_report',
                'bg'    => "linear-gradient(135deg, {$brandBlue}, #1e3a8a)",
            ],
        ];
    @endphp

    {{-- Center the whole launcher nicely --}}
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center">
        <div class="w-full max-w-6xl">

            {{-- Header --}}
            <div class="flex flex-col items-center text-center gap-2">
                <div class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    التطبيقات
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    اختر التطبيق المطلوب من القائمة أدناه
                </div>
            </div>

            {{-- Odoo-like background --}}
            <div class="mt-8 rounded-3xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
                <div
                    class="p-8 sm:p-10"
                    style="
                        background:
                        radial-gradient(900px 500px at 70% 0%, rgba(9,183,219,.16), transparent 55%),
                        radial-gradient(900px 500px at 30% 0%, rgba(214,161,58,.14), transparent 60%),
                        linear-gradient(180deg, rgba(5,54,136,.06), transparent 60%);
                    "
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 place-items-stretch">
                        @foreach ($apps as $app)
                            <a
                                href="{{ $app['url'] }}"
                                class="group rounded-2xl border border-gray-200/80 dark:border-gray-800/80
                                       bg-white/70 dark:bg-gray-950/40 backdrop-blur
                                       hover:bg-white dark:hover:bg-gray-950 transition
                                       hover:shadow-xl hover:-translate-y-0.5 transform
                                       p-6 flex items-center gap-4"
                            >
                                {{-- Icon badge --}}
                                <div
                                    class="shrink-0 w-14 h-14 rounded-2xl grid place-items-center shadow-md ring-1 ring-white/30"
                                    style="background: {{ $app['bg'] }};"
                                >
                                    @php
                                        // Map icon key to blade component tag: x-app-icons.{name}
                                        $iconComponent = 'app-icons.' . $app['icon'];
                                    @endphp

                                    <x-dynamic-component :component="$iconComponent" class="w-7 h-7 text-white opacity-95" />
                                </div>

                                {{-- Text --}}
                                <div class="min-w-0">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $app['label'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">
                                        {{ $app['desc'] }}
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="ms-auto text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 text-xs text-gray-500 dark:text-gray-400 text-center">
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-filament-panels::page>
