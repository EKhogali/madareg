<?php

namespace App\Filament\Pages;

use App\Models\Group;
use App\Models\Subscriber;
use App\Models\FollowUpPeriod;
use App\Models\FollowUpItem;
use App\Models\FollowUpTemplate;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopManagementMonthlyReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'تقرير الإدارة العليا';
    protected static ?string $navigationGroup = 'المتابعة';
    protected static ?int $navigationSort = 30;

    protected static string $view = 'filament.pages.top-management-monthly-report';

    /** Filters */
    public ?int $groupId = null;
    public ?int $templateId = null;
    public ?int $stageId = null;
    public ?int $trackDegreeId = null;

    public int $year;
    public int $month;

    /** KPI results */
    public array $kpis = [
        'active_subscribers' => 0,
        'periods_count' => 0,
        'locked_periods' => 0,
        'daily_pct' => 0,
        'weekly_pct' => 0,
        'monthly_pct' => 0,
        'total_pct' => 0,
    ];

    /** Ranking results */
    public array $topGroups = [];
    public array $topSubscribers = [];
    public array $topTemplates = [];

    public array $chartGender = ['labels' => [], 'data' => []];
public array $chartGroups = ['labels' => [], 'data' => []];
public array $chartStages = ['labels' => [], 'data' => []];
public array $chartTracks = ['labels' => [], 'data' => []];


    public function getTitle(): string
    {
        return 'تقرير الإدارة العليا';
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && in_array((int) $user->role, [1, 2, 3], true); // staff only
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $now = now();
        $this->year = (int) $now->year;
        $this->month = (int) $now->month;

        $this->buildReport();
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Grid::make()
                ->columns(['default' => 1, 'md' => 6])
                ->schema([

                    Forms\Components\Select::make('groupId')
                        ->label('المجموعة')
                        ->preload()
                        ->options(fn () => $this->groupOptions())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport())
                        ->searchable(),

                    Forms\Components\Select::make('templateId')
                        ->label('نموذج المتابعة')
                        ->preload()
                        ->options(fn () => FollowUpTemplate::query()
                            ->where('is_active', true)
                            ->orderBy('name_ar')
                            ->pluck('name_ar', 'id')
                            ->toArray()
                        )
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport())
                        ->searchable(),

                    Forms\Components\Select::make('stageId')
                        ->label('المرحلة')
                        ->preload()
                        ->options(fn () => DB::table('stages')->orderBy('name')->pluck('name', 'id')->toArray())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport())
                        ->searchable(),

                    Forms\Components\Select::make('trackDegreeId')
                        ->label('المسار')
                        ->preload()
                        ->options(fn () => DB::table('track_degrees')->orderBy('title')->pluck('title', 'id')->toArray())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport())
                        ->searchable(),

                    Forms\Components\Select::make('month')
                        ->label('الشهر')
                        ->options($this->monthOptions())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport()),

                    Forms\Components\Select::make('year')
                        ->label('السنة')
                        ->options($this->yearOptions())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport()),
                ]),
        ]);
    }

    protected function groupOptions(): array
    {
        $query = Group::query()->orderBy('name');

        // Supervisor sees only their groups
        if (Auth::user()?->role === 3) {
            $groupIds = Auth::user()->groups()->pluck('groups.id');
            $query->whereIn('id', $groupIds);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    protected function monthOptions(): array
    {
        return [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];
    }

    protected function yearOptions(): array
    {
        $current = (int) now()->year;

        return collect(range($current - 1, $current + 1))
            ->mapWithKeys(fn ($y) => [$y => (string) $y])
            ->toArray();
    }

    /** ✅ Main Builder */
    public function buildReport(): void
    {
        $this->resetResults();

        [$isCurrentMonth, $daysLimit, $weekLimit] = $this->limitsForMonth($this->year, $this->month);

        // Step A) Subscribers scope (filters + role)
        $subsQuery = Subscriber::query()
            ->where('active', 1);

        if ($this->groupId) {
            $subsQuery->where('group_id', $this->groupId);
        }

        if ($this->templateId) {
            $subsQuery->where('follow_up_template_id', $this->templateId);
        }

        if ($this->stageId) {
            $subsQuery->where('stage_id', $this->stageId);
        }

        if ($this->trackDegreeId) {
            $subsQuery->where('track_degree_id', $this->trackDegreeId);
        }

        // Supervisor restriction
        if (Auth::user()?->role === 3) {
            $groupIds = Auth::user()->groups()->pluck('groups.id');
            $subsQuery->whereIn('group_id', $groupIds);
        }

        $subscriberIds = $subsQuery->pluck('id');
        if ($subscriberIds->isEmpty()) {
            return;
        }

        $this->kpis['active_subscribers'] = $subscriberIds->count();

        // Step B) Periods for selected month/year
        $periodsQuery = FollowUpPeriod::query()
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('year', $this->year)
            ->where('month', $this->month);

        $periodIds = $periodsQuery->pluck('id');
        $this->kpis['periods_count'] = $periodIds->count();

        if ($periodIds->isEmpty()) {
            return;
        }

        $this->kpis['locked_periods'] = (clone $periodsQuery)->where('is_month_locked', true)->count();

        // Step C) KPI totals
        $this->computeOverallKpis($periodIds, $daysLimit, $weekLimit);

        // Step D) Rankings
        $this->buildTopGroups($subscriberIds, $daysLimit, $weekLimit);
        $this->buildTopSubscribers($subscriberIds, $daysLimit, $weekLimit);
        $this->buildTopTemplates($subscriberIds, $daysLimit, $weekLimit);
$this->buildDemographicsCharts($subscriberIds);

        
    }


    protected function buildDemographicsCharts($subscriberIds): void
{
    // Gender: assumes 1=Male,2=Female
    $genderRows = Subscriber::query()
        ->select('gender', DB::raw('COUNT(*) as total'))
        ->whereIn('id', $subscriberIds)
        ->groupBy('gender')
        ->get();

    $genderMap = [
        1 => 'ذكور',
        2 => 'إناث',
        null => 'غير محدد',
    ];

    $labels = [];
    $data = [];

    foreach ($genderRows as $row) {
        $labels[] = $genderMap[$row->gender] ?? 'غير محدد';
        $data[] = (int) $row->total;
    }

    $this->chartGender = ['labels' => $labels, 'data' => $data];

    // Count per group
    $groupRows = Subscriber::query()
        ->select('group_id', DB::raw('COUNT(*) as total'))
        ->whereIn('id', $subscriberIds)
        ->groupBy('group_id')
        ->get();

    $groupNames = Group::query()
        ->pluck('name', 'id')
        ->toArray();

    $labels = [];
    $data = [];

    foreach ($groupRows as $row) {
        $labels[] = $row->group_id ? ($groupNames[$row->group_id] ?? 'غير معروف') : 'بدون مجموعة';
        $data[] = (int) $row->total;
    }

    $this->chartGroups = ['labels' => $labels, 'data' => $data];

    // Count per stage
    $stageRows = Subscriber::query()
        ->select('stage_id', DB::raw('COUNT(*) as total'))
        ->whereIn('id', $subscriberIds)
        ->groupBy('stage_id')
        ->get();

    $stageNames = DB::table('stages')->pluck('name', 'id')->toArray();

    $labels = [];
    $data = [];

    foreach ($stageRows as $row) {
        $labels[] = $row->stage_id ? ($stageNames[$row->stage_id] ?? 'غير معروف') : 'بدون مرحلة';
        $data[] = (int) $row->total;
    }

    $this->chartStages = ['labels' => $labels, 'data' => $data];

    // Count per track degree
    $trackRows = Subscriber::query()
        ->select('track_degree_id', DB::raw('COUNT(*) as total'))
        ->whereIn('id', $subscriberIds)
        ->groupBy('track_degree_id')
        ->get();

    $trackNames = DB::table('track_degrees')->pluck('title', 'id')->toArray();

    $labels = [];
    $data = [];

    foreach ($trackRows as $row) {
        $labels[] = $row->track_degree_id ? ($trackNames[$row->track_degree_id] ?? 'غير معروف') : 'بدون مسار';
        $data[] = (int) $row->total;
    }

    $this->chartTracks = ['labels' => $labels, 'data' => $data];
}


    protected function resetResults(): void
    {
        $this->kpis = [
            'active_subscribers' => 0,
            'periods_count' => 0,
            'locked_periods' => 0,
            'daily_pct' => 0,
            'weekly_pct' => 0,
            'monthly_pct' => 0,
            'total_pct' => 0,
        ];

        $this->topGroups = [];
        $this->topSubscribers = [];
        $this->topTemplates = [];

        $this->chartGender = ['labels' => [], 'data' => []];
$this->chartGroups = ['labels' => [], 'data' => []];
$this->chartStages = ['labels' => [], 'data' => []];
$this->chartTracks = ['labels' => [], 'data' => []];

    }

    protected function limitsForMonth(int $year, int $month): array
    {
        $now = now();

        $isCurrentMonth = ($year === (int) $now->year && $month === (int) $now->month);
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $daysLimit = $isCurrentMonth ? (int) $now->day : $daysInMonth;
        $weekLimit = $isCurrentMonth ? (int) ceil($daysLimit / 7) : 5;

        return [$isCurrentMonth, $daysLimit, $weekLimit];
    }

    protected function computeOverallKpis($periodIds, int $daysLimit, int $weekLimit): void
    {
        $periods = FollowUpPeriod::query()
            ->whereIn('id', $periodIds)
            ->get(['id', 'follow_up_template_id']);

        $templateIds = $periods->pluck('follow_up_template_id')->unique();

        $items = FollowUpItem::query()
            ->whereIn('follow_up_template_id', $templateIds)
            ->where('is_active', true)
            ->get(['follow_up_template_id', 'frequency']);

        $expectedDaily = 0;
        $expectedWeekly = 0;
        $expectedMonthly = 0;

        foreach ($periods as $period) {
            $tplItems = $items->where('follow_up_template_id', $period->follow_up_template_id);
            $dailyCount = $tplItems->where('frequency', 1)->count();
            $weeklyCount = $tplItems->where('frequency', 2)->count();
            $monthlyCount = $tplItems->where('frequency', 3)->count();

            $expectedDaily += $dailyCount * $daysLimit;
            $expectedWeekly += $weeklyCount * $weekLimit;
            $expectedMonthly += $monthlyCount;
        }

        $doneDaily = DB::table('follow_up_entries')
            ->whereIn('follow_up_period_id', $periodIds)
            ->whereNotNull('date')
            ->where('value', 1)
            ->whereDate('date', '<=', Carbon::createFromDate($this->year, $this->month, $daysLimit)->toDateString())
            ->count();

        $doneWeekly = DB::table('follow_up_entries')
            ->whereIn('follow_up_period_id', $periodIds)
            ->whereNull('date')
            ->whereNotNull('week_index')
            ->where('week_index', '<=', $weekLimit)
            ->where('value', 1)
            ->count();

        $doneMonthly = DB::table('follow_up_entries')
            ->whereIn('follow_up_period_id', $periodIds)
            ->whereNull('date')
            ->whereNull('week_index')
            ->where('value', 1)
            ->count();

        $this->kpis['daily_pct'] = $expectedDaily > 0 ? round(($doneDaily / $expectedDaily) * 100, 1) : 0;
        $this->kpis['weekly_pct'] = $expectedWeekly > 0 ? round(($doneWeekly / $expectedWeekly) * 100, 1) : 0;
        $this->kpis['monthly_pct'] = $expectedMonthly > 0 ? round(($doneMonthly / $expectedMonthly) * 100, 1) : 0;

        $expectedTotal = $expectedDaily + $expectedWeekly + $expectedMonthly;
        $doneTotal = $doneDaily + $doneWeekly + $doneMonthly;

        $this->kpis['total_pct'] = $expectedTotal > 0 ? round(($doneTotal / $expectedTotal) * 100, 1) : 0;
    }

    protected function buildTopGroups($subscriberIds, int $daysLimit, int $weekLimit): void
    {
        $subs = Subscriber::query()
            ->whereIn('id', $subscriberIds)
            ->get(['id', 'group_id']);

        $periods = FollowUpPeriod::query()
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get(['id', 'subscriber_id', 'follow_up_template_id']);

        $periodBySubscriber = $periods->keyBy('subscriber_id');
        $templateIds = $periods->pluck('follow_up_template_id')->unique();

        $items = FollowUpItem::query()
            ->whereIn('follow_up_template_id', $templateIds)
            ->where('is_active', true)
            ->get(['follow_up_template_id', 'frequency']);

        $groupAgg = [];

        foreach ($subs as $sub) {
            $period = $periodBySubscriber->get($sub->id);
            if (!$period) continue;

            $tplItems = $items->where('follow_up_template_id', $period->follow_up_template_id);
            $dailyCount = $tplItems->where('frequency', 1)->count();
            $weeklyCount = $tplItems->where('frequency', 2)->count();
            $monthlyCount = $tplItems->where('frequency', 3)->count();

            $expected = ($dailyCount * $daysLimit) + ($weeklyCount * $weekLimit) + $monthlyCount;
            if ($expected <= 0) continue;

            $done = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->where('value', 1)
                ->count();

            $pct = round(($done / $expected) * 100, 1);

            $gid = (int) ($sub->group_id ?? 0);
            if (!isset($groupAgg[$gid])) {
                $groupAgg[$gid] = ['sum' => 0, 'count' => 0];
            }
            $groupAgg[$gid]['sum'] += $pct;
            $groupAgg[$gid]['count'] += 1;
        }

        $groupNames = Group::query()
            ->whereIn('id', array_filter(array_keys($groupAgg)))
            ->pluck('name', 'id')
            ->toArray();

        $rows = [];
        foreach ($groupAgg as $gid => $agg) {
            $avg = $agg['count'] > 0 ? round($agg['sum'] / $agg['count'], 1) : 0;

            $rows[] = [
                'group' => $gid === 0 ? 'بدون مجموعة' : ($groupNames[$gid] ?? 'غير معروف'),
                'avg_total_pct' => $avg,
                'subs_count' => $agg['count'],
            ];
        }

        usort($rows, fn ($a, $b) => $b['avg_total_pct'] <=> $a['avg_total_pct']);
        $this->topGroups = array_slice($rows, 0, 10);
    }

    protected function buildTopSubscribers($subscriberIds, int $daysLimit, int $weekLimit): void
    {
        $subs = Subscriber::query()
            ->whereIn('id', $subscriberIds)
            ->with('group:id,name')
            ->get(['id', 'name', 'group_id']);

        $periods = FollowUpPeriod::query()
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get(['id', 'subscriber_id', 'follow_up_template_id'])
            ->keyBy('subscriber_id');

        $templateIds = $periods->pluck('follow_up_template_id')->unique();

        $items = FollowUpItem::query()
            ->whereIn('follow_up_template_id', $templateIds)
            ->where('is_active', true)
            ->get(['follow_up_template_id', 'frequency']);

        $rows = [];

        foreach ($subs as $sub) {
            $period = $periods->get($sub->id);
            if (!$period) continue;

            $tplItems = $items->where('follow_up_template_id', $period->follow_up_template_id);
            $dailyCount = $tplItems->where('frequency', 1)->count();
            $weeklyCount = $tplItems->where('frequency', 2)->count();
            $monthlyCount = $tplItems->where('frequency', 3)->count();

            $expected = ($dailyCount * $daysLimit) + ($weeklyCount * $weekLimit) + $monthlyCount;
            if ($expected <= 0) continue;

            $done = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->where('value', 1)
                ->count();

            $pct = round(($done / $expected) * 100, 1);

            $rows[] = [
                'subscriber' => $sub->name,
                'group' => $sub->group?->name ?? '-',
                'total_pct' => $pct,
            ];
        }

        usort($rows, fn ($a, $b) => $b['total_pct'] <=> $a['total_pct']);
        $this->topSubscribers = array_slice($rows, 0, 10);
    }

    protected function buildTopTemplates($subscriberIds, int $daysLimit, int $weekLimit): void
    {
        $subs = Subscriber::query()
            ->whereIn('id', $subscriberIds)
            ->whereNotNull('follow_up_template_id')
            ->get(['id', 'follow_up_template_id']);

        $periods = FollowUpPeriod::query()
            ->whereIn('subscriber_id', $subscriberIds)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get(['id', 'subscriber_id', 'follow_up_template_id'])
            ->keyBy('subscriber_id');

        $templateAgg = [];

        $templateIds = $periods->pluck('follow_up_template_id')->unique();

        $items = FollowUpItem::query()
            ->whereIn('follow_up_template_id', $templateIds)
            ->where('is_active', true)
            ->get(['follow_up_template_id', 'frequency']);

        foreach ($subs as $sub) {
            $period = $periods->get($sub->id);
            if (!$period) continue;

            $tplItems = $items->where('follow_up_template_id', $period->follow_up_template_id);
            $dailyCount = $tplItems->where('frequency', 1)->count();
            $weeklyCount = $tplItems->where('frequency', 2)->count();
            $monthlyCount = $tplItems->where('frequency', 3)->count();

            $expected = ($dailyCount * $daysLimit) + ($weeklyCount * $weekLimit) + $monthlyCount;
            if ($expected <= 0) continue;

            $done = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->where('value', 1)
                ->count();

            $pct = round(($done / $expected) * 100, 1);

            $tid = (int) $period->follow_up_template_id;
            if (!isset($templateAgg[$tid])) {
                $templateAgg[$tid] = ['sum' => 0, 'count' => 0];
            }
            $templateAgg[$tid]['sum'] += $pct;
            $templateAgg[$tid]['count'] += 1;
        }

        $templateNames = FollowUpTemplate::query()
            ->whereIn('id', array_keys($templateAgg))
            ->pluck('name_ar', 'id')
            ->toArray();

        $rows = [];
        foreach ($templateAgg as $tid => $agg) {
            $avg = $agg['count'] > 0 ? round($agg['sum'] / $agg['count'], 1) : 0;

            $rows[] = [
                'template' => $templateNames[$tid] ?? 'غير معروف',
                'avg_total_pct' => $avg,
                'subs_count' => $agg['count'],
            ];
        }

        usort($rows, fn ($a, $b) => $b['avg_total_pct'] <=> $a['avg_total_pct']);
        $this->topTemplates = array_slice($rows, 0, 10);
    }
}
