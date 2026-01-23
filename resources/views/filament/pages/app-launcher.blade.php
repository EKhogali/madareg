<x-filament-panels::page>
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
           
        </div>

        @php
            $u = auth()->user();

            $tiles = [
                [
                    'title' => 'المشتركين',
                    'subtitle' => 'البيانات الأساسية',
                    'icon' => asset('images/launcher/subscribers.png'),
                    'url' => \App\Filament\Resources\SubscriberResource::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'أولياء الأمور',
                    'subtitle' => 'إضافة وتعديل',
                    'icon' => asset('images/launcher/parents.png'),
                    'url' => \App\Filament\Resources\ParentResource::getUrl(),
                    'can' => true,
                ],
                [
                    'title' => 'المتابعة الشهرية',
                    'subtitle' => 'نموذج المتابعة',
                    'icon' => asset('images/launcher/followup.png'),
                    'url' => \App\Filament\Pages\FollowUpMonthlySheet::getUrl(),
                    'can' => true,
                ],

                
                [
                    'title' => 'تقرير المتابعة الشهرية',
                    'subtitle' => 'تقارير المتابعة',
                    'icon' => asset('images/launcher/report.png'),
                    'url' => \App\Filament\Pages\MonthlyFollowUpReport::getUrl(),
                    'can' => true,
                ],

                // Super admin only tiles:
                [
                    'title' => 'الأنشطة',
                    'subtitle' => 'إدارة الأنشطة',
                    'icon' => asset('images/launcher/activities.png'),
                    'url' => \App\Filament\Resources\ActivityResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'المجموعات',
                    'subtitle' => 'البيانات الأساسية',
                    'icon' => asset('images/launcher/groups.png'),
                    'url' => \App\Filament\Resources\GroupResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'المستخدمون',
                    'subtitle' => 'إدارة النظام',
                    'icon' => asset('images/launcher/users.png'),
                    'url' => \App\Filament\Resources\UserResource::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
                [
                    'title' => 'تقرير الإدارة العليا',
                    'subtitle' => 'لوحة مؤشرات',
                    'icon' => asset('images/launcher/top-report.png'),
                    'url' => \App\Filament\Pages\TopManagementMonthlyReport::getUrl(),
                    'can' => $u?->isSuperAdmin(),
                ],
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 place-items-center">
            @foreach ($tiles as $tile)
                @continue(!($tile['can'] ?? false))

                <a href="{{ $tile['url'] }}"
                   class="group w-full max-w-[200px] aspect-square rounded-3xl border border-gray-200 bg-white p-5
                          shadow-sm transition hover:shadow-lg hover:-translate-y-0.5
                          focus:outline-none focus:ring-2 focus:ring-primary-500">

                    <div class="h-full flex flex-col items-center justify-center text-center gap-3">
                        <div class="h-16 w-16 rounded-2xl bg-gray-50 ring-1 ring-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ $tile['icon'] }}"
                                 alt=""
                                 class="h-10 w-10 object-contain"
                                 onerror="this.remove(); this.parentElement.innerHTML='📌'; this.parentElement.classList.add('text-2xl');" />
                        </div>

                        <div>
                            <div class="text-base font-semibold text-gray-900">{{ $tile['title'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $tile['subtitle'] }}</div>
                        </div>

                        <div class="text-gray-300 group-hover:text-gray-600 transition text-xl">›</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
