<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'تغيير كلمة المرور';
    protected static ?string $title = 'تغيير كلمة المرور';
    protected static ?string $slug = 'change-password';
    protected static string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('تغيير كلمة المرور')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::min(8))
                            ->same('password_confirmation'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $user = auth()->user();
        $data = $this->form->getState();

        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('كلمة المرور الحالية غير صحيحة')
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'password' => $data['password'], // hashed cast will hash it
        ]);

        $this->form->fill([]);

        Notification::make()
            ->title('تم تغيير كلمة المرور بنجاح')
            ->success()
            ->send();
    }
}
