<x-filament-panels::page>

    {{ $this->form }}

    {{-- Tabs --}}
    <div class="mt-6">
        <div class="flex flex-wrap gap-2">
            <button
                class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-900 text-white"
                x-data
                x-on:click="$dispatch('set-dashboard-tab', { tab: 'overview' })"
            >نظرة عامة</button>

            <button
                class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-100 dark:bg-gray-900"
                x-data
                x-on:click="$dispatch('set-dashboard-tab', { tab: 'demographics' })"
            >التوزيع السكاني</button>

            <button
                class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-100 dark:bg-gray-900"
                x-data
                x-on:click="$dispatch('set-dashboard-tab', { tab: 'performance' })"
            >الأداء</button>
        </div>
    </div>

    <div
        x-data="{ tab: 'overview' }"
        x-on:set-dashboard-tab.window="tab = $event.detail.tab"
        class="mt-6"
    >
        {{-- Overview --}}
        <div x-show="tab === 'overview'" class="space-y-6">

            {{-- KPI Cards - Power BI style --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">عدد المشتركين النشطين</div>
                    <div class="text-3xl font-extrabold mt-2">{{ $this->kpis['active_subscribers'] }}</div>
                </div>

                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">عدد السجلات المنشأة</div>
                    <div class="text-3xl font-extrabold mt-2">{{ $this->kpis['periods_count'] }}</div>
                </div>

                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">عدد السجلات المقفولة</div>
                    <div class="text-3xl font-extrabold mt-2">{{ $this->kpis['locked_periods'] }}</div>
                </div>

                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">نسبة الإنجاز الإجمالية</div>
                    <div class="text-3xl font-extrabold mt-2">{{ $this->kpis['total_pct'] }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">نسبة الإنجاز اليومية</div>
                    <div class="text-2xl font-bold mt-2">{{ $this->kpis['daily_pct'] }}%</div>
                </div>

                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">نسبة الإنجاز الأسبوعية</div>
                    <div class="text-2xl font-bold mt-2">{{ $this->kpis['weekly_pct'] }}%</div>
                </div>

                <div class="rounded-2xl p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="text-xs text-gray-500">نسبة الإنجاز الشهرية</div>
                    <div class="text-2xl font-bold mt-2">{{ $this->kpis['monthly_pct'] }}%</div>
                </div>
            </div>

            {{-- Rankings --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                    <div class="font-bold mb-3">أفضل المجموعات</div>
                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-2 text-right border-b">المجموعة</th>
                                    <th class="px-3 py-2 text-center border-b">المتوسط %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->topGroups as $row)
                                    <tr class="border-b">
                                        <td class="px-3 py-2 text-right">{{ $row['group'] }}</td>
                                        <td class="px-3 py-2 text-center font-bold">{{ $row['avg_total_pct'] }}%</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                    <div class="font-bold mb-3">أفضل المشتركين</div>
                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-2 text-right border-b">المشترك</th>
                                    <th class="px-3 py-2 text-center border-b">النسبة %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->topSubscribers as $row)
                                    <tr class="border-b">
                                        <td class="px-3 py-2 text-right">{{ $row['subscriber'] }}</td>
                                        <td class="px-3 py-2 text-center font-bold">{{ $row['total_pct'] }}%</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                    <div class="font-bold mb-3">أفضل النماذج</div>
                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-2 text-right border-b">النموذج</th>
                                    <th class="px-3 py-2 text-center border-b">المتوسط %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->topTemplates as $row)
                                    <tr class="border-b">
                                        <td class="px-3 py-2 text-right">{{ $row['template'] }}</td>
                                        <td class="px-3 py-2 text-center font-bold">{{ $row['avg_total_pct'] }}%</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- Demographics --}}
        <div x-show="tab === 'demographics'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-dashboard-bar-chart
                    chartId="genderChart"
                    title="عدد المشتركين حسب الجنس"
                    :labels="$this->chartGender['labels']"
                    :data="$this->chartGender['data']"
                />

                <x-dashboard-bar-chart
                    chartId="groupChart"
                    title="عدد المشتركين حسب المجموعة"
                    :labels="$this->chartGroups['labels']"
                    :data="$this->chartGroups['data']"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-dashboard-bar-chart
                    chartId="stageChart"
                    title="عدد المشتركين حسب المرحلة"
                    :labels="$this->chartStages['labels']"
                    :data="$this->chartStages['data']"
                />

                <x-dashboard-bar-chart
                    chartId="trackChart"
                    title="عدد المشتركين حسب المسار"
                    :labels="$this->chartTracks['labels']"
                    :data="$this->chartTracks['data']"
                />
            </div>
        </div>

        {{-- Performance (future expand) --}}
        <div x-show="tab === 'performance'">
            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
                <div class="font-bold">قادم قريباً</div>
                <div class="text-sm text-gray-500 mt-2">
                    هنا سنضيف توزيع نسب الإنجاز (0-25، 25-50، 50-75، 75-100) واتجاهات الأداء عبر الشهور.
                </div>
            </div>
        </div>
    </div>

</x-filament-panels::page>
