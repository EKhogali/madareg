<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;



class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'المشتركين';
    protected static ?string $modelLabel = 'مشترك';
    protected static ?string $pluralModelLabel = 'المشتركين';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make(__('Personal Information'))
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('image_path')
                                ->label(__('subscriber_image'))
                                ->directory('subscribers')
                                ->disk('public')
                                ->image()
                                ->imageEditor()
                                ->visibility('public')
                                ->preserveFilenames()
                                ->maxSize(2048),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Personal Information'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label(__('name'))->required(),
                            DatePicker::make('birth_date')->label(__('birth_date')),
                            TextInput::make('birth_place')->label(__('birth_place')),
                            TextInput::make('residence_place')
                                ->label(__('residence_place'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('residence_place')->pluck('residence_place')->filter()->values()->all()),
                            TextInput::make('nationality')
                                ->label(__('nationality'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('nationality')->pluck('nationality')->filter()->values()->all()),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Education'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('study_level')
                                ->label(__('study_level'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('study_level')->pluck('study_level')->filter()->values()->all()),
                            Select::make('education_type')->label(__('education_type'))->options([
                                0 => __('education_type_public'),
                                1 => __('education_type_private'),
                                2 => __('education_type_international'),
                            ]),
                            TextInput::make('school_name')->label(__('school_name')),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Quran Memorization'))
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('is_quran_student')->label(__('is_quran_student')),
                            TextInput::make('quran_amount')->label(__('quran_amount')),
                            TextInput::make('quran_memorization_center')
                                ->label(__('quran_memorization_center'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('quran_memorization_center')->pluck('quran_memorization_center')->filter()->values()->all()),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Talents'))
                    ->schema([
                        Textarea::make('talents')->label(__('talents')),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Social Info'))
                    ->schema([
                        Select::make('social_status')->label(__('social_status'))->options([
                            0 => __('social_with_parents'),
                            1 => __('orphan_father'),
                            2 => __('orphan_mother'),
                            3 => __('divorced_mother'),
                            4 => __('divorced_father'),
                            5 => __('divorced_maternal_grandparents'),
                            6 => __('divorced_paternal_grandparents'),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Parents\' Work'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('father_job')
                                ->label(__('father_job'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('father_job')->pluck('father_job')->filter()->values()->all()),
                            Select::make('father_job_type')->label(__('father_job_type'))->options([
                                0 => __('unemployed'),
                                1 => __('public_sector'),
                                2 => __('private_sector'),
                                3 => __('retired'),
                            ]),
                            TextInput::make('mother_job')
                                ->label(__('mother_job'))
                                ->datalist(Subscriber::query()->distinct()->whereNotNull('mother_job')->pluck('mother_job')->filter()->values()->all()),
                            Select::make('mother_job_type')->label(__('mother_job_type'))->options([
                                0 => __('unemployed'),
                                1 => __('public_sector'),
                                2 => __('private_sector'),
                                3 => __('retired'),
                            ]),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Health'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('health_status')->label(__('health_status'))->options([
                                0 => __('health_good'),
                                1 => __('health_bad'),
                            ]),
                            TextInput::make('disease_type')->label(__('disease_type')),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Relatives'))
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('has_relatives_at_madareg_administration')
                                ->label(__('has_relatives_at_madareg_administration')),
                            TextInput::make('relatives_at_madareg_administration')
                                ->label(__('relatives_at_madareg_administration')),
                            Toggle::make('has_relatives_at_madareg')
                                ->label(__('has_relatives_at_madareg')),
                            TextInput::make('relatives_at_madareg')
                                ->label(__('relatives_at_madareg')),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Contact'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('father_phone')->label(__('father_phone')),
                            TextInput::make('mother_phone')->label(__('mother_phone')),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),

                Section::make(__('Status'))
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('active')->label(__('active')),
                            Toggle::make('locked')->label(__('locked')),
                        ]),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color: #EBF8FF; padding: 1rem; border-radius: 8px;'
                    ]),
            ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('image_path')
                    ->label(__('subscriber_image'))
                    ->circular()
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(asset('images/default-user.png')),

                // Always visible
                TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('birth_date')
                    ->label(__('birth_date'))
                    ->date(),

                TextColumn::make('study_level')
                    ->label(__('study_level')),

                BadgeColumn::make('education_type')
                    ->label(__('education_type'))
                    ->colors([
                        'gray' => 0,
                        'success' => 1,
                        'info' => 2,
                    ])
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        0 => __('education_type_public'),
                        1 => __('education_type_private'),
                        2 => __('education_type_international'),
                        default => '—',
                    }),

                ToggleColumn::make('is_quran_student')
                    ->label(__('is_quran_student')),

                BadgeColumn::make('health_status')
                    ->label(__('health_status'))
                    ->colors([
                        'success' => 0,
                        'danger' => 1,
                    ])
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        0 => __('health_good'),
                        1 => __('health_bad'),
                        default => '—',
                    }),

                // Toggleable extra fields
                TextColumn::make('birth_place')->label(__('birth_place'))->toggleable(),
                TextColumn::make('residence_place')->label(__('residence_place'))->toggleable(),
                TextColumn::make('nationality')->label(__('nationality'))->toggleable(),
                TextColumn::make('school_name')->label(__('school_name'))->toggleable(),
                TextColumn::make('quran_amount')->label(__('quran_amount'))->toggleable(),
                TextColumn::make('quran_memorization_center')->label(__('quran_memorization_center'))->toggleable(),
                TextColumn::make('talents')->label(__('talents'))->toggleable(),

                TextColumn::make('social_status')
                    ->label(__('social_status'))
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        0 => __('social_with_parents'),
                        1 => __('orphan_father'),
                        2 => __('orphan_mother'),
                        3 => __('divorced_mother'),
                        4 => __('divorced_father'),
                        5 => __('divorced_maternal_grandparents'),
                        6 => __('divorced_paternal_grandparents'),
                        default => '—',
                    })
                    ->toggleable(),

                TextColumn::make('father_job')->label(__('father_job'))->toggleable(),
                TextColumn::make('father_job_type')
                    ->label(__('father_job_type'))
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        0 => __('unemployed'),
                        1 => __('public_sector'),
                        2 => __('private_sector'),
                        3 => __('retired'),
                        default => '—',
                    })
                    ->toggleable(),

                TextColumn::make('mother_job')->label(__('mother_job'))->toggleable(),
                TextColumn::make('mother_job_type')
                    ->label(__('mother_job_type'))
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        0 => __('unemployed'),
                        1 => __('public_sector'),
                        2 => __('private_sector'),
                        3 => __('retired'),
                        default => '—',
                    })
                    ->toggleable(),

                TextColumn::make('disease_type')->label(__('disease_type'))->toggleable(),
                ToggleColumn::make('has_relatives_at_madareg_administration')->label(__('has_relatives_at_madareg_administration'))->toggleable(),
                TextColumn::make('relatives_at_madareg_administration')->label(__('relatives_at_madareg_administration'))->toggleable(),
                ToggleColumn::make('has_relatives_at_madareg')->label(__('has_relatives_at_madareg'))->toggleable(),
                TextColumn::make('relatives_at_madareg')->label(__('relatives_at_madareg'))->toggleable(),
                TextColumn::make('father_phone')->label(__('father_phone'))->toggleable(),
                TextColumn::make('mother_phone')->label(__('mother_phone'))->toggleable(),
                ToggleColumn::make('active')
                    ->label(__('active'))
                    ->toggleable(),

                ToggleColumn::make('locked')
                    ->label(__('locked'))
                    ->toggleable(),

            ])

            ->filters([
                SelectFilter::make('education_type')
                    ->label(__('education_type'))
                    ->options([
                        0 => __('education_type_public'),
                        1 => __('education_type_private'),
                        2 => __('education_type_international'),
                    ]),

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

            ->actions([
                Tables\Actions\ViewAction::make()->label(__('View')),
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Delete selected')),
            ]);
    }





    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
