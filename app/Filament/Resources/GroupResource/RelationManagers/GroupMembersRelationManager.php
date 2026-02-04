<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Filament\Resources\SubscriberResource;
use App\Models\Subscriber;
use App\Models\FollowUpTemplate;
use App\Models\Stage;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GroupMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'subscribers';
    protected static ?string $title = 'الأعضاء';

    /**
     * SuperAdmin: always
     * Supervisor: only their assigned groups
     */
    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        $u = auth()->user();
        if (!$u) return false;

        if ($u->isSuperAdmin()) return true;

        if ($u->isSupervisor()) {
            $groupIds = $u->groups()->pluck('groups.id')->toArray();
            return in_array($ownerRecord->id, $groupIds, true);
        }

        return false;
    }

    // You said: edit/add subscriber from SubscriberResource, not from relation manager
    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ✅ Filament v4: allow header actions on View page
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        $isSuperAdmin = auth()->user()?->isSuperAdmin() ?? false;
        $isSupervisor = auth()->user()?->isSupervisor() ?? false;
        $canAttachDetach = $isSuperAdmin || $isSupervisor;

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // extra safety: always show only subscribers of THIS group
                return $query->where('group_id', $this->getOwnerRecord()->id);
            })
            ->columns([
                

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date(),

                TextColumn::make('group.name')
                    ->label('المجموعة')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('join_date')
                    ->label('تاريخ الانضمام')
                    ->date(),

                ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->circular()
                    ->disk('public')
                    ->size(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('followUpTemplate.name_ar')
                    ->label('نموذج المتابعة')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('stage.name')
                    ->label('المرحلة')
                    ->formatStateUsing(fn ($state) => $state ?? 'غير محددة')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('track_degree_id')
                    ->label('مجموع الدرجات')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('trackDegree.title')
                    ->label('درجة المضمار')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('study_level')
                    ->label('المرحلة الدراسية')
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('education_type')
                    ->label('نوع التعليم')
                    ->colors([
                        'gray' => 0,
                        'success' => 1,
                        'info' => 2,
                    ])
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => __('education_type_public'),
                        1 => __('education_type_private'),
                        2 => __('education_type_international'),
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_quran_student')
                    ->label(__('is_quran_student'))
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('health_status')
                    ->label(__('health_status'))
                    ->colors([
                        'success' => 0,
                        'danger' => 1,
                    ])
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => __('health_good'),
                        1 => __('health_bad'),
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // ===== toggleable extra columns (same as resource) =====
                TextColumn::make('birth_place')->label(__('birth_place'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('residence_place')->label(__('residence_place'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nationality')->label(__('nationality'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('school_name')->label(__('school_name'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quran_amount')->label(__('quran_amount'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quran_memorization_center')->label(__('quran_memorization_center'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('talents')->label(__('talents'))->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('social_status')
                    ->label(__('social_status'))
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => __('social_with_parents'),
                        1 => __('orphan_father'),
                        2 => __('orphan_mother'),
                        3 => __('divorced_mother'),
                        4 => __('divorced_father'),
                        5 => __('divorced_maternal_grandparents'),
                        6 => __('divorced_paternal_grandparents'),
                        default => '—',
                    })
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('father_job')->label(__('father_job'))->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('father_job_type')
                    ->label(__('father_job_type'))
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => __('unemployed'),
                        1 => __('public_sector'),
                        2 => __('private_sector'),
                        3 => __('retired'),
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('mother_job')->label(__('mother_job'))->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('mother_job_type')
                    ->label(__('mother_job_type'))
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => __('unemployed'),
                        1 => __('public_sector'),
                        2 => __('private_sector'),
                        3 => __('retired'),
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('disease_type')->label(__('disease_type'))->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('has_relatives_at_madareg_administration')->label(__('has_relatives_at_madareg_administration'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('relatives_at_madareg_administration')->label(__('relatives_at_madareg_administration'))->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('has_relatives_at_madareg')->label(__('has_relatives_at_madareg'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('relatives_at_madareg')->label(__('relatives_at_madareg'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('father_phone')->label(__('father_phone'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mother_phone')->label(__('mother_phone'))->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('active')->label(__('active'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('education_type')
                    ->label(__('education_type'))
                    ->options([
                        0 => __('education_type_public'),
                        1 => __('education_type_private'),
                        2 => __('education_type_international'),
                    ]),

                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        1 => __('Male'),
                        2 => __('Female'),
                    ]),

                SelectFilter::make('track_degree_id')
                    ->label(__('Track'))
                    ->relationship('trackDegree', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('follow_up_template_id')
                    ->label('نموذج المتابعة')
                    ->options(
                        FollowUpTemplate::query()
                            ->orderBy('name_ar')
                            ->pluck('name_ar', 'id')
                            ->toArray()
                    )
                    ->searchable(),

                Filter::make('join_date')
                    ->form([
                        DatePicker::make('from')->label(__('From')),
                        DatePicker::make('until')->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('join_date', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('join_date', '<=', $date));
                    }),

                SelectFilter::make('stage_id')
                    ->label('المرحلة')
                    ->options(
                        Stage::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('health_status')
                    ->label(__('health_status'))
                    ->options([
                        0 => __('health_good'),
                        1 => __('health_bad'),
                    ]),

                SelectFilter::make('social_status')
                    ->label(__('social_status'))
                    ->options([
                        0 => __('social_with_parents'),
                        1 => __('orphan_father'),
                        2 => __('orphan_mother'),
                        3 => __('divorced_mother'),
                        4 => __('divorced_father'),
                        5 => __('divorced_maternal_grandparents'),
                        6 => __('divorced_paternal_grandparents'),
                    ]),

                SelectFilter::make('is_quran_student')
                    ->label(__('is_quran_student'))
                    ->options([
                        1 => __('yes'),
                        0 => __('no'),
                    ]),
            ])
            // ✅ Click subscriber row -> Subscriber read-only view page
            ->recordUrl(fn (Subscriber $record) => SubscriberResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                // ✅ Attach existing subscriber to THIS group (sets group_id)
                Tables\Actions\Action::make('attachSubscriber')
                    ->label('إضافة مشترك')
                    ->visible(fn () => $canAttachDetach)
                    ->form([
                        Select::make('subscriber_id')
                            ->label('Subscriber')
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $groupId = $this->getOwnerRecord()->id;

                                return Subscriber::query()
                                    ->where('active', true)
                                    ->where(function ($q) use ($groupId) {
                                        $q->whereNull('group_id')->orWhere('group_id', '!=', $groupId);
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        Subscriber::whereKey($data['subscriber_id'])
                            ->update(['group_id' => $this->getOwnerRecord()->id]);
                    }),
            ])
            ->actions([
                // ✅ Detach: set group_id = null (leave group null)
                Tables\Actions\Action::make('detachSubscriber')
                    ->label('إزالة من المجموعة')
                    ->visible(fn () => $canAttachDetach)
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $record) => $record->update(['group_id' => null])),

                // ✅ Keep edit/delete in relation manager for SuperAdmin only (optional)
                // If you want ZERO edit/delete from here, delete these two lines.
                // Tables\Actions\EditAction::make()->visible(fn () => $isSuperAdmin),
                // Tables\Actions\DeleteAction::make()->visible(fn () => $isSuperAdmin),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn () => $isSuperAdmin),
            ]);
    }

    // extra safety: keep policies
    public function canCreate(): bool { return auth()->user()?->isSuperAdmin() ?? false; }
    public function canEdit(Model $record): bool { return auth()->user()?->isSuperAdmin() ?? false; }
    public function canDelete(Model $record): bool { return auth()->user()?->isSuperAdmin() ?? false; }
}
