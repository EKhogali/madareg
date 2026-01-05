<x-filament-panels::page>

    {{-- Filters --}}
    {{ $this->form }}

    <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-3 py-2 text-right border-b border-gray-200 dark:border-gray-800">المشترك</th>
                    <th class="px-3 py-2 text-right border-b border-gray-200 dark:border-gray-800">القالب</th>
                    <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800">اليومي %</th>
                    <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800">الأسبوعي %</th>
                    <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800">الشهري %</th>
                    <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800 font-bold">الإجمالي %</th>
                    <th class="px-3 py-2 text-center border-b border-gray-200 dark:border-gray-800">الحالة</th>
                </tr>
            </thead>

            <tbody>
                @forelse($this->reportRows as $row)
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <td class="px-3 py-2 text-right">{{ $row['subscriber'] }}</td>
                        <td class="px-3 py-2 text-right">{{ $row['template'] }}</td>

                        <td class="px-3 py-2 text-center">{{ $row['daily'] }}%</td>
                        <td class="px-3 py-2 text-center">{{ $row['weekly'] }}%</td>
                        <td class="px-3 py-2 text-center">{{ $row['monthly'] }}%</td>

                        <td class="px-3 py-2 text-center font-bold">{{ $row['total'] }}%</td>

                        <td class="px-3 py-2 text-center">
                            <span class="px-2 py-1 rounded-lg text-xs
                                {{ $row['status'] === 'مقفول' ? 'bg-danger-100 text-danger-700' : 'bg-success-100 text-success-700' }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            لا توجد بيانات مطابقة للفلاتر.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-filament-panels::page>
