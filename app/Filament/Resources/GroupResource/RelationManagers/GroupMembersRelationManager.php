<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Models\User;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class GroupMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';
    protected static ?string $title = 'الأعضاء';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Select existing user or create new one
            Select::make('user_id')
                ->label('العضو')
                ->options(User::where('role', User::ROLE_MEMBER)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Section::make('معلومات العضو')
                        ->schema([
                            FileUpload::make('image')
                                ->label('صورة العضو')
                                ->directory('users')
                                ->image()
                                ->imagePreviewHeight('100'),

                            TextInput::make('name')->label('الاسم')->required(),
                            TextInput::make('email')->label('البريد الإلكتروني')->email()->unique('users', 'email')->required(),
                            TextInput::make('password')->label('كلمة المرور')->password()->required()->default('password'),
                        ])->columns(2),

                    // 🟢 Subscriber details (full profile)
                    Section::make('بيانات المشترك')
                        ->schema([
                            DatePicker::make('birth_date')->label('تاريخ الميلاد'),
                            TextInput::make('birth_place')->label('مكان الميلاد'),
                            TextInput::make('residence_place')->label('مكان الإقامة'),
                            TextInput::make('nationality')->label('الجنسية'),
                            TextInput::make('study_level')->label('المرحلة الدراسية'),
                            Select::make('education_type')
                                ->label('نوع التعليم')
                                ->options([
                                    0 => 'حكومي',
                                    1 => 'خاص',
                                    2 => 'دولي',
                                ]),
                            TextInput::make('school_name')->label('اسم المدرسة'),
                        ])->columns(2),
                ])
                ->createOptionUsing(function (array $data) {
                    // 🧠 Step 1: create user
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'role' => User::ROLE_MEMBER,
                        'image' => $data['image'] ?? null,
                    ]);

                    // 🧠 Step 2: create subscriber linked to this user
                    Subscriber::create([
                        'user_id' => $user->id,
                        'name' => $data['name'],
                        'birth_date' => $data['birth_date'] ?? null,
                        'birth_place' => $data['birth_place'] ?? null,
                        'residence_place' => $data['residence_place'] ?? null,
                        'nationality' => $data['nationality'] ?? null,
                        'study_level' => $data['study_level'] ?? null,
                        'education_type' => $data['education_type'] ?? null,
                        'school_name' => $data['school_name'] ?? null,
                    ]);

                    return $user;
                })
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('الصورة')->circular(),
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('subscriber.study_level')->label('المرحلة الدراسية'),
                TextColumn::make('subscriber.birth_date')->label('تاريخ الميلاد')->date(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة عضو'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}



