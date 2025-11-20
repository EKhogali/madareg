<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use App\Models\ActivityDetail;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ViewActivity extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery()
    {
        return ActivityDetail::query()
            ->where('activity_id', $this->record->id)
            ->with('subscriber');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('subscriber.name')->label('العضو'),
            Tables\Columns\TextColumn::make('evaluation')->label('التقييم'),
            Tables\Columns\TextColumn::make('notes')->label('ملاحظات'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إضافة تقييم جديد')
                ->model(ActivityDetail::class)
                ->using(function (array $data, $record) {
                    $data['activity_id'] = $this->record->id;
                    $record->fill($data)->save();
                    return $record;
                }),
        ];
    }
}
