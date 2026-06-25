<?php

namespace App\Filament\Pages;

use App\Models\Subscriber;
use App\Models\Group;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Traits\HasLauncherBackAction;

class SubscriberProgress extends Page
{
    use HasLauncherBackAction;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'مسار التقدم';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.subscriber-progress';
    protected static ?string $slug = 'subscriber-progress';

    public ?int $filterSubscriberId = null;
    public array $progressData = [];

    // ─── Stage definitions (200 points each, 5 stages = 1000 total) ───
    public static array $stages = [
        1 => ['name' => 'بصيص',  'min' => 1,   'max' => 200,  'color' => '#3B82F6', 'emoji' => '✨'],
        2 => ['name' => 'بريق',  'min' => 201,  'max' => 400,  'color' => '#8B5CF6', 'emoji' => '⚡'],
        3 => ['name' => 'ضياء',  'min' => 401,  'max' => 600,  'color' => '#F59E0B', 'emoji' => '🌟'],
        4 => ['name' => 'وميض',  'min' => 601,  'max' => 800,  'color' => '#EF4444', 'emoji' => '🔥'],
        5 => ['name' => 'نور',   'min' => 801,  'max' => 1000, 'color' => '#10B981', 'emoji' => '☀️'],
    ];

    // ─── 10 milestone titles from seeder ───
    public static array $milestones = [
        ['min' => 1,   'max' => 100,  'title' => 'إحماء'],
        ['min' => 101, 'max' => 200,  'title' => 'خطوة 1'],
        ['min' => 201, 'max' => 300,  'title' => 'خطوة 2'],
        ['min' => 301, 'max' => 400,  'title' => 'مرحلة'],
        ['min' => 401, 'max' => 500,  'title' => 'إنجاز'],
        ['min' => 501, 'max' => 600,  'title' => 'تقدُّم'],
        ['min' => 601, 'max' => 700,  'title' => 'ركض'],
        ['min' => 701, 'max' => 800,  'title' => 'جري'],
        ['min' => 801, 'max' => 900,  'title' => 'سباق'],
        ['min' => 901, 'max' => 1000, 'title' => 'فوز'],
    ];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // shown via AppLauncher tile instead
    }

    public function getTitle(): string
    {
        return 'مسار التقدم — مدارج النور';
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
        $this->loadProgress();
    }

    // ─── Scope subscribers based on role ───
    protected function subscribersQuery(): Builder
    {
        $u = Auth::user();
        $q = Subscriber::query()
            ->with(['trackDegree', 'stage', 'group'])
            ->where('active', true);

        $role = (int) $u->role;

        if ($role === 5) {
            // Subscriber: only themselves
            return $q->where('subscriber_user_id', $u->id);
        }

        if ($role === 4) {
            // Parent: only their children
            return $q->where('user_id', $u->id);
        }

        if ($role === 3) {
            // Supervisor: only their groups
            $groupIds = $u->groups()->pluck('groups.id')->toArray();
            return $q->whereIn('group_id', $groupIds);
        }

        // SuperAdmin / Monitor: all
        return $q;
    }

    public function loadProgress(): void
    {
        $query = $this->subscribersQuery();

        if ($this->filterSubscriberId) {
            $query->where('id', $this->filterSubscriberId);
        }

        $subscribers = $query->orderByDesc('track_degree_id')->get();

        $this->progressData = $subscribers->map(function (Subscriber $s) {
            $points = (int) ($s->track_degree_id ?? 0);
            $pct    = min(100, round(($points / 1000) * 100, 1));

            // Current stage
            $currentStage = null;
            foreach (self::$stages as $stage) {
                if ($points >= $stage['min'] && $points <= $stage['max']) {
                    $currentStage = $stage;
                    break;
                }
            }
            if (!$currentStage) {
                $currentStage = $points >= 1000
                    ? self::$stages[5]
                    : self::$stages[1];
            }

            // Current milestone title
            $milestoneTitle = $s->trackDegree?->title ?? 'إحماء';

            // Points to next stage
            $nextStagePoints = null;
            foreach (self::$stages as $stage) {
                if ($points < $stage['min']) {
                    $nextStagePoints = $stage['min'] - $points;
                    break;
                }
            }

            // Stage progress within current stage (0–100)
            $stageMin = $currentStage['min'];
            $stageMax = $currentStage['max'];
            $stagePct = min(100, round((($points - $stageMin + 1) / ($stageMax - $stageMin + 1)) * 100, 1));

            return [
                'id'              => $s->id,
                'name'            => $s->name,
                'image'           => $s->image_path,
                'points'          => $points,
                'pct'             => $pct,
                'stage'           => $currentStage,
                'stage_pct'       => $stagePct,
                'milestone_title' => $milestoneTitle,
                'next_stage_pts'  => $nextStagePoints,
                'group'           => $s->group?->name ?? '—',
            ];
        })->toArray();
    }

    public function updatedFilterSubscriberId(): void
    {
        $this->loadProgress();
    }

    // ─── Filter options for the select ───
    public function subscriberOptions(): array
    {
        return $this->subscribersQuery()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
        ];
    }
}