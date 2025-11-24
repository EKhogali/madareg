<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'المستخدمون';
    protected static ?string $pluralModelLabel = 'المستخدمون';
    protected static ?string $modelLabel = 'مستخدم';
    

    //     protected static ?string $navigationLabel = 'المشتركين';
// protected static ?string $pluralModelLabel = 'المشتركين';
// protected static ?string $modelLabel = 'مشترك';
    protected static ?string $navigationGroup = 'إدارة النظام'; // Optional grouping
    // protected static int $navigationSort = 2; // Optional order

    // ✅ FORM (Create / Edit)
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المستخدم')
                ->schema([
                    FileUpload::make('image')
                        ->label('صورة المستخدم')
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
                        ->unique(ignoreRecord: true)
                        ->required(),

                    TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                        ->required(fn(string $context): bool => $context === 'create')
                        ->revealable(),

                    Select::make('role')
                        ->label('الدور')
                        ->options([
                            1 => 'مشرف عام (Super Admin)',
                            2 => 'مدير (Admin)',
                            3 => 'مشرف (Supervisor)',
                            4 => 'عضو (Member)',
                        ])
                        ->default(4)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    // ✅ TABLE (List View)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        1 => 'Super Admin',
                        2 => 'Admin',
                        3 => 'Supervisor',
                        4 => 'Member',
                        default => 'Unknown',
                    })
                    ->colors([
                        'success' => 1,
                        'warning' => 2,
                        'info' => 3,
                        'gray' => 4,
                    ]),

                TextColumn::make('groups.name')
                    ->label('المجموعات')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('الدور')
                    ->options([
                        1 => 'Super Admin',
                        2 => 'Admin',
                        3 => 'Supervisor',
                        4 => 'Member',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // ✅ Define Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
