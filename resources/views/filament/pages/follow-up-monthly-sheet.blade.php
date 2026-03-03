<x-filament-panels::page>

    {{-- Filters --}}
    {{ $this->form }}

    @if (!$subscriberId)
        <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                لا يوجد مشتركون مرتبطون بهذا الحساب.
            </p>
        </div>

    @elseif (!$period)
        <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                هذا المشترك لا يحتوي على نموذج متابعة (Template) — يرجى تعيين follow_up_template_id للمشترك.
            </p>
        </div>

    @else

        @php
            /**
             * ✅ Daily Columns Ordering
             * Only for template 2:
             *
             * Order must be:
             * الفجر, راتبة الفجر, الظهر, راتبة الظهر, العصر, راتبة العصر, المغرب, راتبة المغرب, العشاء, راتبة العشاء
             */

            $dailyItemsSorted = $dailyItems;

            if ((int) $period->follow_up_template_id === 2) {

                // Prayer order
                $prayerOrder = [
                    'الفجر' => 1,
                    'الظهر' => 2,
                    'العصر' => 3,
                    'المغرب' => 4,
                    'العشاء' => 5,
                ];

                // Helper to detect prayer
                $detectPrayer = function (string $name) use ($prayerOrder): int {
                    foreach ($prayerOrder as $prayer => $idx) {
                        if (str_contains($name, $prayer)) {
                            return $idx;
                        }
                    }
                    return 99; // unknown goes last
                };

                // Helper: detect if it's "راتبة"
                $isRaatiba = function (string $name): int {
                    // ratiba should come AFTER the main prayer
                    return str_contains($name, 'راتبة') ? 2 : 1;
                };

                $dailyItemsSorted = $dailyItems->sortBy(function ($item) use ($detectPrayer, $isRaatiba) {
                    $name = $item->name_ar ?? '';

                    $prayerIndex = $detectPrayer($name);
                    $typeIndex = $isRaatiba($name);
                    $sortOrder = (int) ($item->sort_order ?? 0);

                    // prayer first, then (prayer vs ratiba), then sort_order
                    return ($prayerIndex * 10000) + ($typeIndex * 100) + $sortOrder;
                })->values();
            }

            // Weekly/monthly keep as-is
            $weeklyItemsSorted = $weeklyItems;
            $monthlyItemsSorted = $monthlyItems;
        @endphp


        {{-- Summary --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                <div class="text-sm text-gray-600 dark:text-gray-300">أيام الشهر</div>
                <div class="text-xl font-bold">{{ $this->daysInMonth() }}</div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                <div class="text-sm text-gray-600 dark:text-gray-300">الحالة</div>
                <div class="text-xl font-bold">
                    {{ $period->is_month_locked ? 'مقفول' : 'مفتوح' }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                <div class="text-sm text-gray-600 dark:text-gray-300">القالب</div>
                <div class="text-xl font-bold">
                    {{ $period->template?->name_ar ?? '-' }}
                </div>
            </div>
        </div>


        {{-- Actions --}}
        @php
            $canToggleLock = auth()->user()?->isSuperAdmin() || auth()->user()?->isSupervisor();
        @endphp

        <div class="mt-6 flex flex-wrap items-center gap-3">

            {{-- ✅ Manual Save --}}
            <x-filament::button wire:click="save" icon="heroicon-o-check">
                حفظ
            </x-filament::button>

            {{-- Month Lock --}}
            @if ($canToggleLock)
                @if (!$period->is_month_locked)
                    <x-filament::button color="warning" wire:click="toggleLock('month', null, true)" icon="heroicon-o-lock-closed">
                        قفل الشهر
                    </x-filament::button>
                @else
                    <x-filament::button color="gray" wire:click="toggleLock('month', null, false)" icon="heroicon-o-lock-open">
                        فتح الشهر
                    </x-filament::button>
                @endif
            @endif

            @if(auth()->user()?->isSuperAdmin() && $this->period)
    <x-filament::button 
        wire:click="resetSubscriberPeriod" 
        wire:confirm="هل أنت متأكد؟ سيتم حذف جميع إدخالات هذا الشهر لهذا المشترك لتمكين تغيير النموذج."
        color="danger" 
        icon="heroicon-o-trash"
        size="sm">
        إعادة تعيين الشهر (لتغيير النموذج)
    </x-filament::button>
@endif

            <div class="text-sm font-semibold {{ $period->is_month_locked ? 'text-danger-600' : 'text-success-600' }}">
                {{ $period->is_month_locked ? 'الشهر مقفول' : 'الشهر مفتوح' }}
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                ✅ اختر ثم اضغط حفظ
            </div>
        </div>


        {{-- Desktop --}}
        <div class="mt-6 hidden md:block">
            <div class="overflow-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="sticky right-0 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-right border-b border-gray-200 dark:border-gray-800">
                                اليوم
                            </th>

                            @foreach ($dailyItemsSorted as $item)
                                <th
                                    class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800 whitespace-nowrap">
                                    {{ $item->name_ar }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @for ($day = 1; $day <= $this->daysInMonth(); $day++)
                            @php
                                $weekIndexRow = (int) ceil($day / 7);
                                $rowLocked = $period->isWeekLocked($weekIndexRow);
                            @endphp

                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <td
                                    class="sticky right-0 bg-white dark:bg-gray-950 px-3 py-2 text-right font-semibold whitespace-nowrap">
                                    {{ $day }}
                                    @if($rowLocked)
                                        <span class="text-xs text-danger-600">🔒</span>
                                    @endif
                                </td>

                                @foreach ($dailyItemsSorted as $item)
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                            wire:model.defer="state.daily.{{ $day }}.{{ $item->id }}"
                                            @disabled($period->is_month_locked || $rowLocked) />
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- Weekly + Monthly --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Weekly --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <div class="font-bold mb-3">أعمال الأسبوع</div>

                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-2 text-right border-b border-gray-200 dark:border-gray-800">
                                        الأسبوع
                                    </th>
                                    @foreach ($weeklyItemsSorted as $item)
                                        <th
                                            class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800 whitespace-nowrap">
                                            {{ $item->name_ar }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @for ($w = 1; $w <= 5; $w++)
                                    @php $lockedWeek = $period->isWeekLocked($w); @endphp

                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    الأسبوع {{ $w }}
                                                    @if($lockedWeek)
                                                        <span class="text-xs text-danger-600 font-bold">(مقفول)</span>
                                                    @else
                                                        <span class="text-xs text-success-600 font-bold">(مفتوح)</span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-1">
                                                    @if ($canToggleLock)
                                                        @if ($lockedWeek)
                                                            <x-filament::button size="xs" color="gray" icon="heroicon-o-lock-open"
                                                                wire:click="toggleLock('week', {{ $w }}, false)"
                                                                :disabled="$period->is_month_locked">
                                                                فتح
                                                            </x-filament::button>
                                                        @else
                                                            <x-filament::button size="xs" color="warning" icon="heroicon-o-lock-closed"
                                                                wire:click="toggleLock('week', {{ $w }}, true)"
                                                                :disabled="$period->is_month_locked">
                                                                قفل
                                                            </x-filament::button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        @foreach ($weeklyItemsSorted as $item)
                                            <td class="px-3 py-2 text-center">
                                                <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                                    wire:model.defer="state.weekly.{{ $w }}.{{ $item->id }}"
                                                    @disabled($period->is_month_locked || $lockedWeek) />
                                            </td>
                                        @endforeach
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Monthly --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <div class="font-bold mb-3">أعمال الشهر</div>

                    <div class="space-y-3">
                        @foreach ($monthlyItemsSorted as $item)
                            <label class="flex items-center gap-3">
                                <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                    wire:model.defer="state.monthly.{{ $item->id }}" @disabled($period->is_month_locked) />
                                <span>{{ $item->name_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- ✅ Mobile --}}
        <div class="mt-6 md:hidden space-y-6">

            {{-- Day Controls --}}
            @php
                $mobileDay = (int) ($this->mobileDay ?? 1);
                $mobileWeekIndex = (int) ceil($mobileDay / 7);
                $mobileWeekLocked = $period->isWeekLocked($mobileWeekIndex);
                $mobileLocked = $period->is_month_locked || $mobileWeekLocked;
            @endphp

            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                <div class="flex items-center justify-between gap-3">
                    <div class="font-bold">
                        اليوم {{ $mobileDay }}
                        <span class="text-xs text-gray-500"> (الأسبوع {{ $mobileWeekIndex }})</span>
                        @if($mobileLocked)
                            <span class="text-xs text-danger-600 font-bold">🔒 مقفول</span>
                        @else
                            <span class="text-xs text-success-600 font-bold">✅ مفتوح</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <x-filament::button size="sm" color="gray" wire:click="prevDay" icon="heroicon-o-chevron-right">
                            السابق
                        </x-filament::button>

                        <x-filament::button size="sm" color="gray" wire:click="nextDay" icon="heroicon-o-chevron-left">
                            التالي
                        </x-filament::button>
                    </div>
                </div>

                {{-- Jump to day --}}
                <div class="mt-4">
                    <label class="text-xs text-gray-500">اذهب إلى يوم</label>
                    <input type="number" min="1" max="{{ $this->daysInMonth() }}"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900"
                        wire:model.lazy="mobileDay" />
                </div>

                {{-- Week lock/unlock for current mobile week --}}
                <div class="mt-4 flex items-center justify-between gap-2">
                    <div class="text-sm font-semibold">
                        قفل الأسبوع {{ $mobileWeekIndex }}
                    </div>

                    <div class="flex items-center gap-2">
                        @if($canToggleLock)
                            @if($mobileWeekLocked)
                                <x-filament::button size="sm" color="gray" icon="heroicon-o-lock-open"
                                    wire:click="toggleLock('week', {{ $mobileWeekIndex }}, false)"
                                    :disabled="$period->is_month_locked">
                                    فتح
                                </x-filament::button>
                            @else
                                <x-filament::button size="sm" color="warning" icon="heroicon-o-lock-closed"
                                    wire:click="toggleLock('week', {{ $mobileWeekIndex }}, true)"
                                    :disabled="$period->is_month_locked">
                                    قفل
                                </x-filament::button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Daily Items (for the selected day) --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                <div class="font-bold mb-3">أعمال اليوم</div>

                <div class="space-y-3">
                    @foreach ($dailyItemsSorted as $item)
                        <label
                            class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="text-sm font-semibold">
                                {{ $item->name_ar }}
                            </div>

                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                wire:model.defer="state.daily.{{ $mobileDay }}.{{ $item->id }}" @disabled($mobileLocked) />
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Weekly Items (for the selected day week) --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-bold">أعمال الأسبوع {{ $mobileWeekIndex }}</div>
                    <div class="text-xs {{ $mobileWeekLocked ? 'text-danger-600' : 'text-success-600' }}">
                        {{ $mobileWeekLocked ? 'مقفول' : 'مفتوح' }}
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($weeklyItemsSorted as $item)
                        <label
                            class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="text-sm font-semibold">
                                {{ $item->name_ar }}
                            </div>

                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                wire:model.defer="state.weekly.{{ $mobileWeekIndex }}.{{ $item->id }}"
                                @disabled($period->is_month_locked || $mobileWeekLocked) />
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Monthly Items --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
                <div class="font-bold mb-3">أعمال الشهر</div>

                <div class="space-y-3">
                    @foreach ($monthlyItemsSorted as $item)
                        <label
                            class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="text-sm font-semibold">
                                {{ $item->name_ar }}
                            </div>

                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700"
                                wire:model.defer="state.monthly.{{ $item->id }}" @disabled($period->is_month_locked) />
                        </label>
                    @endforeach
                </div>
            </div>

        </div>


    @endif

</x-filament-panels::page>