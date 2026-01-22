<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Support\Traits\HasLauncherBackAction;

class MyProfile extends Page
{
    use HasLauncherBackAction;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'ملفي الشخصي';
    protected static ?string $title = 'ملفي الشخصي';
    protected static ?string $slug = 'my-profile';
    protected static string $view = 'filament.pages.my-profile';

    public ?array $data = [];

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            ...parent::getHeaderActions(),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return false; // we will add it to user menu
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'name'  => $user->name,
            'phone' => $user->phone ?? null,
            'image' => $user->image ?? null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('بياناتي')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('الهاتف')
                            ->maxLength(30),

                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->disk('public')
                            ->directory('users')
                            ->image()
                            ->imageEditor()
                            ->visibility('public')
                            ->maxSize(2048),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $user = auth()->user();
        $data = $this->form->getState();

        if (!empty($data['image']) && $data['image'] !== $user->image) {
            if (!empty($user->image) && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
        }

        $user->update([
            'name'  => $data['name'],
            'phone' => $data['phone'] ?? null,
            'image' => $data['image'] ?? null,
        ]);

        Notification::make()
            ->title('تم حفظ بياناتك بنجاح')
            ->success()
            ->send();
    }
}
