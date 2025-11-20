<x-filament::page>
    <x-filament::section>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="font-bold">عنوان المنشط</dt>
                <dd>{{ $record->title }}</dd>
            </div>
            <div>
                <dt class="font-bold">التصنيف</dt>
                <dd>{{ $record->category }}</dd>
            </div>
            <div>
                <dt class="font-bold">من</dt>
                <dd>{{ $record->from_date }}</dd>
            </div>
            <div>
                <dt class="font-bold">إلى</dt>
                <dd>{{ $record->to_date }}</dd>
            </div>
            <div class="col-span-2">
                <dt class="font-bold">الوصف</dt>
                <dd>{{ $record->description }}</dd>
            </div>
        </dl>
    </x-filament::section>

    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>
</x-filament::page>
