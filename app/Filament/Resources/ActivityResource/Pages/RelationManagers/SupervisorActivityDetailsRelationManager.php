<?php

// app/Filament/Resources/ActivityResource/RelationManagers/SupervisorActivityDetailsRelationManager.php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;

class SupervisorActivityDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisorActivityDetails';
    protected static ?string $title = 'مشرفي النشاط';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supervisor.name')->label('اسم المشرف'),
                // Tables\Columns\BadgeColumn::make('activity_role')
                //     ->label('الدور')
                //     ->colors([
                //         'primary' => fn($state) => $state == 2,
                //         'warning' => fn($state) => $state == 1,
                //     ])
                //     ->formatStateUsing(function ($state) {
                //         return match ($state) {
                //             1 => 'مشرف عام',
                //             2 => 'مشرف',
                //             default => 'غير معروف',
                //         };
                //     }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة مشرف')
                    ->form([
                        Forms\Components\Select::make('supervisor_id')
                            ->label('المشرف')
                            ->options(User::where('role', 3)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        // Forms\Components\Select::make('activity_role')
                        //     ->label('الدور')
                        //     ->options([
                        //         2 => 'مشرف',
                        //         1 => 'مشرف عام',
                        //     ])
                        //     ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

