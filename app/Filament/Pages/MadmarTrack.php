<?php

namespace App\Filament\Pages;

use App\Models\Subscriber;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Support\Traits\HasLauncherBackAction;

class MadmarTrack extends Page
{
    use HasLauncherBackAction;

    protected static ?string $navigationIcon  = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'المضمار';
    protected static ?string $navigationGroup = null;
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.madmar-track';
    protected static ?string $slug            = 'madmar-track';

    // Filter
    public ?int $filterGroupId = null;

    // Data passed to view
    public array $milestones   = [];
    public array $subscribers  = [];

    // The 10 milestones matching the seeder
    protected static array $MILESTONES = [
        ['id' => 1,  'range_min' => 1,   'range_max' => 100,  'title' => 'إحماء',    'label' => 'م100'],
        ['id' => 2,  'range_min' => 101,  'range_max' => 200,  'title' => 'خطوة 1',   'label' => 'م200'],
        ['id' => 3,  'range_min' => 201,  'range_max' => 300,  'title' => 'خطوة 2',   'label' => 'م300'],
        ['id' => 4,  'range_min' => 301,  'range_max' => 400,  'title' => 'مرحلة',    'label' => 'م400'],
        ['id' => 5,  'range_min' => 401,  'range_max' => 500,  'title' => 'إنجاز',    'label' => 'م500'],
        ['id' => 6,  'range_min' => 501,  'range_max' => 600,  'title' => 'تقدُّم',   'label' => 'م600'],
        ['id' => 7,  'range_min' => 601,  'range_max' => 700,  'title' => 'ركض',      'label' => 'م700'],
        ['id' => 8,  'range_min' => 701,  'range_max' => 800,  'title' => 'جري',      'label' => 'م800'],
        ['id' => 9,  'range_min' => 801,  'range_max' => 900,  'title' => 'سباق',     'label' => 'م900'],
        ['id' => 10, 'range_min' => 901,  'range_max' => 1000, 'title' => 'فوز',      'label' => 'م1000'],
    ];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'المضمار — مدارج النور';
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
        $this->milestones = self::$MILESTONES;
        $this->loadSubscribers();
    }

    protected function subscribersQuery(): Builder
    {
        $u    = Auth::user();
        $role = (int) $u?->role;

        $q = Subscriber::query()
            ->where('active', true)
            ->with(['group']);

        if ($role === 5) {
            return $q->where('subscriber_user_id', $u->id);
        }

        if ($role === 4) {
            return $q->where('user_id', $u->id);
        }

        if ($role === 3) {
            $groupIds = $u->groups()->pluck('groups.id')->toArray();
            return $q->whereIn('group_id', $groupIds);
        }

        return $q; // SuperAdmin / Monitor — all
    }

    public function loadSubscribers(): void
    {
        $q = $this->subscribersQuery();

        if ($this->filterGroupId) {
            $q->where('group_id', $this->filterGroupId);
        }

        $subs = $q->orderBy('total_points')->get();

        // Group subscribers by their milestone bucket
        $this->subscribers = [];

        $stageNames = [
            1 => 'بصيص', 2 => 'بريق', 3 => 'ضياء',
            4 => 'وميض', 5 => 'نور',
        ];

        foreach ($subs as $s) {
            $pts = (int) ($s->total_points ?? 0);

            // Find milestone bucket — clean logic, break on first match
            $milestoneId = 1;
            foreach (self::$MILESTONES as $m) {
                if ($pts >= $m['range_min'] && $pts <= $m['range_max']) {
                    $milestoneId = $m['id'];
                    break;
                }
            }
            if ($pts >= 1000) { $milestoneId = 10; }

            $this->subscribers[] = [
                'id'        => $s->id,
                'name'      => $s->name,
                'image'     => $s->image_path,
                'points'    => $pts,
                'milestone' => $milestoneId,
                'group'     => $s->group?->name ?? '—',
                'initials'  => mb_substr($s->name, 0, 1),
                'stage'     => $stageNames[$s->stage_id ?? 1] ?? '—',
                'gender'    => (int) ($s->gender ?? 0),
                'study'     => $s->study_level ?? '—',
                'join_date' => $s->join_date
                                ? \Carbon\Carbon::parse($s->join_date)->format('Y-m-d')
                                : '—',
                'pct'       => min(100, round(($pts / 1000) * 100, 1)),
            ];
        }
    }

    public function updatedFilterGroupId(): void
    {
        $this->loadSubscribers();
    }

    public function groupOptions(): array
    {
        $u    = Auth::user();
        $role = (int) $u?->role;

        $q = \App\Models\Group::query()->orderBy('name');

        if ($role === 3) {
            $ids = $u->groups()->pluck('groups.id')->toArray();
            $q->whereIn('id', $ids);
        }

        return $q->pluck('name', 'id')->toArray();
    }
}