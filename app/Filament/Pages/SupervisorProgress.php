<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Support\Traits\HasLauncherBackAction;

class SupervisorProgress extends Page
{
    use HasLauncherBackAction;

    protected static ?string $navigationIcon  = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'مضمار المشرفين';
    protected static ?string $navigationGroup = null;
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.pages.supervisor-progress';
    protected static ?string $slug            = 'supervisor-progress';

    public array $supervisors = [];

    // 10 milestones same as subscriber track
    protected static array $MILESTONES = [
        ['id' => 1,  'min' => 1,   'max' => 100,  'label' => 'م100',  'title' => 'إحماء'],
        ['id' => 2,  'min' => 101,  'max' => 200,  'label' => 'م200',  'title' => 'خطوة 1'],
        ['id' => 3,  'min' => 201,  'max' => 300,  'label' => 'م300',  'title' => 'خطوة 2'],
        ['id' => 4,  'min' => 301,  'max' => 400,  'label' => 'م400',  'title' => 'مرحلة'],
        ['id' => 5,  'min' => 401,  'max' => 500,  'label' => 'م500',  'title' => 'إنجاز'],
        ['id' => 6,  'min' => 501,  'max' => 600,  'label' => 'م600',  'title' => 'تقدُّم'],
        ['id' => 7,  'min' => 601,  'max' => 700,  'label' => 'م700',  'title' => 'ركض'],
        ['id' => 8,  'min' => 701,  'max' => 800,  'label' => 'م800',  'title' => 'جري'],
        ['id' => 9,  'min' => 801,  'max' => 900,  'label' => 'م900',  'title' => 'سباق'],
        ['id' => 10, 'min' => 901,  'max' => 1000, 'label' => 'م1000', 'title' => 'فوز'],
    ];

    public static function canAccess(): bool
    {
        return in_array((int) auth()->user()?->role, [1, 2, 3], true);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'مضمار المشرفين — مدارج النور';
    }

    public function mount(): void
    {
        abort_unless(Auth::check() && static::canAccess(), 403);
        $this->loadSupervisors();
    }

    public function loadSupervisors(): void
    {
        $u    = Auth::user();
        $role = (int) $u?->role;

        $query = User::whereIn('role', [2, 3])
            ->orderByDesc('total_points')
            ->orderBy('name');

        // Role 3: only themselves
        if ($role === 3) {
            $query->where('id', $u->id);
        }

        $supervisors = $query->get();
        $total       = $supervisors->count();

        $this->supervisors = $supervisors->map(function (User $sup, int $index) use ($total) {
            $points = (int) ($sup->total_points ?? 0);
            $pct    = min(100, round(($points / 1000) * 100, 1));

            // Milestone title
            $milestoneTitle = 'لم يبدأ بعد';
            foreach (self::$MILESTONES as $m) {
                if ($points >= $m['min'] && $points <= $m['max']) {
                    $milestoneTitle = $m['title'];
                    break;
                }
            }
            if ($points >= 1000) $milestoneTitle = 'فوز 🏆';

            // Rank medal
            $medal = match($index) {
                0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#' . ($index + 1),
            };

            // Role label
            $roleLabel = match((int) $sup->role) {
                1 => 'سوبر أدمن',
                2 => 'مراقب',
                3 => 'مشرف',
                default => '—',
            };

            return [
                'id'         => $sup->id,
                'name'       => $sup->name,
                'points'     => $points,
                'pct'        => $pct,
                'milestone'  => $milestoneTitle,
                'rank'       => $index + 1,
                'medal'      => $medal,
                'role_label' => $roleLabel,
                'is_me'      => $sup->id === Auth::id(),
            ];
        })->toArray();
    }

    public function getMilestones(): array
    {
        return self::$MILESTONES;
    }
}