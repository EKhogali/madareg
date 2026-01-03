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
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-filament::button wire:click="save" icon="heroicon-o-check">
                حفظ
            </x-filament::button>

            @if (!$period->is_month_locked)
                <x-filament::button color="warning" wire:click="lockMonth" icon="heroicon-o-lock-closed">
                    قفل الشهر
                </x-filament::button>
            @else
                <x-filament::button color="gray" wire:click="unlockMonth" icon="heroicon-o-lock-open">
                    فتح الشهر
                </x-filament::button>
            @endif

            <div class="text-sm font-semibold {{ $period->is_month_locked ? 'text-danger-600' : 'text-success-600' }}">
                {{ $period->is_month_locked ? 'الشهر مقفول' : 'الشهر مفتوح' }}
            </div>
        </div>

        {{-- Desktop --}}
        <div class="mt-6 hidden md:block">
            <div class="overflow-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="sticky right-0 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-right border-b border-gray-200 dark:border-gray-800">
                                اليوم
                            </th>

                            @foreach ($dailyItems as $item)
                                <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800 whitespace-nowrap">
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
                                <td class="sticky right-0 bg-white dark:bg-gray-950 px-3 py-2 text-right font-semibold whitespace-nowrap">
                                    {{ $day }}
                                    @if($rowLocked)
                                        <span class="text-xs text-danger-600">🔒</span>
                                    @endif
                                </td>

                                @foreach ($dailyItems as $item)
                                    <td class="px-3 py-2 text-center">
                                        <input
    type="checkbox"
    class="rounded border-gray-300 dark:border-gray-700"
    wire:model.defer="state.daily.{{ $day }}.{{ $item->id }}"
    @disabled($period->is_month_locked || $rowLocked)
/>
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
                                    @foreach ($weeklyItems as $item)
                                        <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800 whitespace-nowrap">
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
    @if ($lockedWeek)
    <x-filament::button
        size="xs"
        color="gray"
        icon="heroicon-o-lock-open"
        wire:click="unlockWeek({{ $w }})"
        :disabled="$period->is_month_locked"
    >
        فتح
    </x-filament::button>
@else
    <x-filament::button
        size="xs"
        color="warning"
        icon="heroicon-o-lock-closed"
        wire:click="lockWeek({{ $w }})"
        :disabled="$period->is_month_locked"
    >
        قفل
    </x-filament::button>
@endif

</div>

                                            </div>
                                        </td>

                                        @foreach ($weeklyItems as $item)
                                            <td class="px-3 py-2 text-center">
                                                <input
                                                    type="checkbox"
                                                    class="rounded border-gray-300 dark:border-gray-700"
                                                    wire:model.defer="state.weekly.{{ $w }}.{{ $item->id }}"
    @disabled($period->is_month_locked || $rowLocked)
                                                />
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
                        @foreach ($monthlyItems as $item)
                            <label class="flex items-center gap-3">
                                
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 dark:border-gray-700"
                                    wire:model.defer="state.monthly.{{ $item->id }}"
                                    @disabled($period->is_month_locked || $rowLocked)
                                />
                                <span>{{ $item->name_ar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- Mobile disabled for now --}}
        <div class="mt-6 md:hidden p-4 rounded-xl border border-gray-200 dark:border-gray-800">
            Mobile view temporarily disabled
        </div>

    @endif

</x-filament-panels::page>
