<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class GroupSupervisorsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisors';
    protected static ?string $title = 'المشرفون';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('المشرف')
                ->options(\App\Models\User::where('role', \App\Models\User::ROLE_SUPERVISOR)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->createOptionForm([
                    FileUpload::make('image')
                        ->label('صورة المشرف')
                        ->image()
                        ->directory('users')
                        ->imagePreviewHeight('100')
                        ->maxSize(1024)
                        ->nullable(),

                    TextInput::make('name')
                        ->label('الاسم')
                        ->required(),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->unique('users', 'email')
                        ->required(),

                    TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->required()
                        ->default('password'),
                ])
                ->createOptionUsing(function (array $data): \App\Models\User {
                    return \App\Models\User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'role' => \App\Models\User::ROLE_SUPERVISOR,
                        'image' => $data['image'] ?? null,
                    ]);
                })
                ->required()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('name')->label('الاسم'),
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular() // makes it round
                    ->height(50)
                    ->width(50)
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                TextColumn::make('name')->label('الاسم')->sortable()->searchable(),
                TextColumn::make('email')->label('البريد الإلكتروني'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة عضو'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

