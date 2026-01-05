<?php

namespace App\Filament\Pages;

use App\Models\Group;
use App\Models\Subscriber;
use App\Models\FollowUpPeriod;
use App\Models\FollowUpItem;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyFollowUpReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'تقرير المتابعة الشهرية';
    protected static ?string $navigationGroup = 'المتابعة';
    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.pages.monthly-follow-up-report';

    /** Filters */
    public ?int $groupId = null;
    public ?int $subscriberId = null;
    public ?int $templateId = null;

    public int $year;
    public int $month;

    /** ✅ KPI column selector */
    public array $selectedKpis = ['daily', 'weekly', 'monthly', 'total', 'status'];

    /** Results */
    public array $reportRows = [];

    public function getTitle(): string
    {
        return 'تقرير المتابعة الشهرية';
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

                    Forms\Components\Select::make('subscriberId')
                        ->label('المشترك')
                        ->preload()
                        ->options(fn () => $this->subscriberOptions())
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->buildReport())
                        ->searchable(),

                    Forms\Components\Select::make('templateId')
                        ->label('نموذج المتابعة')
                        ->preload()
                        ->options(fn () => $this->templateOptions())
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

                    /** ✅ KPI selector */
                    Forms\Components\CheckboxList::make('selectedKpis')
                        ->label('الأعمدة الظاهرة')
                        ->options([
                            'daily' => 'اليومي %',
                            'weekly' => 'الأسبوعي %',
                            'monthly' => 'الشهري %',
                            'total' => 'الإجمالي %',
                            'status' => 'الحالة',
                        ])
                        ->columns(3)
                        ->reactive(),
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

        // Parent (role 4): optional return groups of their subscribers only
        if (Auth::user()?->role === 4) {
            $groupIds = Subscriber::query()
                ->where('user_id', Auth::id())
                ->whereNotNull('group_id')
                ->pluck('group_id')
                ->unique();

            $query->whereIn('id', $groupIds);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    protected function subscriberOptions(): array
    {
        $query = Subscriber::query()->orderBy('name');

        if ($this->groupId) {
            $query->where('group_id', $this->groupId);
        }

        if (Auth::user()?->role === 4) {
            $query->where('user_id', Auth::id());
        }

        if (Auth::user()?->role === 3) {
            $groupIds = Auth::user()->groups()->pluck('groups.id');
            $query->whereIn('group_id', $groupIds);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    protected function templateOptions(): array
    {
        $query = Subscriber::query();

        if ($this->groupId) {
            $query->where('group_id', $this->groupId);
        }

        if (Auth::user()?->role === 4) {
            $query->where('user_id', Auth::id());
        }

        if (Auth::user()?->role === 3) {
            $groupIds = Auth::user()->groups()->pluck('groups.id');
            $query->whereIn('group_id', $groupIds);
        }

        return $query
            ->whereNotNull('follow_up_template_id')
            ->select('follow_up_template_id')
            ->distinct()
            ->with('followUpTemplate:id,name_ar')
            ->get()
            ->pluck('followUpTemplate.name_ar', 'follow_up_template_id')
            ->toArray();
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

    /** ✅ Main report builder */
    public function buildReport(): void
    {
        $this->reportRows = [];

        $now = now();
        $isCurrentMonth = ($this->year === (int) $now->year && $this->month === (int) $now->month);

        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
        $daysLimit = $isCurrentMonth ? (int) $now->day : $daysInMonth;
        $weekLimit = $isCurrentMonth ? (int) ceil($daysLimit / 7) : 5;

        $subsQuery = Subscriber::query()->with('followUpTemplate');

        if ($this->subscriberId) {
            $subsQuery->where('id', $this->subscriberId);
        }

        if ($this->templateId) {
            $subsQuery->where('follow_up_template_id', $this->templateId);
        }

        if ($this->groupId) {
            $subsQuery->where('group_id', $this->groupId);
        }

        if (Auth::user()?->role === 4) {
            $subsQuery->where('user_id', Auth::id());
        }

        if (Auth::user()?->role === 3) {
            $groupIds = Auth::user()->groups()->pluck('groups.id');
            $subsQuery->whereIn('group_id', $groupIds);
        }

        $subscribers = $subsQuery->get();

        foreach ($subscribers as $subscriber) {

            if (!$subscriber->follow_up_template_id) continue;

            $period = FollowUpPeriod::query()
                ->where('subscriber_id', $subscriber->id)
                ->where('year', $this->year)
                ->where('month', $this->month)
                ->first();

            if (!$period) {
                $this->reportRows[] = [
                    'subscriber' => $subscriber->name,
                    'template' => $subscriber->followUpTemplate?->name_ar ?? '-',
                    'daily' => 0,
                    'weekly' => 0,
                    'monthly' => 0,
                    'total' => 0,
                    'status' => 'لا يوجد سجل للشهر',
                ];
                continue;
            }

            $items = FollowUpItem::query()
                ->where('follow_up_template_id', $period->follow_up_template_id)
                ->where('is_active', true)
                ->get(['id', 'frequency']);

            $dailyCount = $items->where('frequency', 1)->count();
            $weeklyCount = $items->where('frequency', 2)->count();
            $monthlyCount = $items->where('frequency', 3)->count();

            $expectedDaily = $dailyCount * $daysLimit;
            $expectedWeekly = $weeklyCount * $weekLimit;
            $expectedMonthly = $monthlyCount;

            $completedDailyQuery = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->whereNotNull('date')
                ->where('value', 1);

            if ($isCurrentMonth) {
                $completedDailyQuery->whereDate(
                    'date',
                    '<=',
                    Carbon::createFromDate($this->year, $this->month, $daysLimit)->toDateString()
                );
            }

            $completedDaily = $completedDailyQuery->count();

            $completedWeeklyQuery = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->whereNull('date')
                ->whereNotNull('week_index')
                ->where('value', 1);

            if ($isCurrentMonth) {
                $completedWeeklyQuery->where('week_index', '<=', $weekLimit);
            }

            $completedWeekly = $completedWeeklyQuery->count();

            $completedMonthly = DB::table('follow_up_entries')
                ->where('follow_up_period_id', $period->id)
                ->whereNull('date')
                ->whereNull('week_index')
                ->where('value', 1)
                ->count();

            $dailyPct = $expectedDaily > 0 ? round(($completedDaily / $expectedDaily) * 100, 1) : 0;
            $weeklyPct = $expectedWeekly > 0 ? round(($completedWeekly / $expectedWeekly) * 100, 1) : 0;
            $monthlyPct = $expectedMonthly > 0 ? round(($completedMonthly / $expectedMonthly) * 100, 1) : 0;

            $expectedTotal = $expectedDaily + $expectedWeekly + $expectedMonthly;
            $completedTotal = $completedDaily + $completedWeekly + $completedMonthly;

            $totalPct = $expectedTotal > 0 ? round(($completedTotal / $expectedTotal) * 100, 1) : 0;

            $this->reportRows[] = [
                'subscriber' => $subscriber->name,
                'template' => $subscriber->followUpTemplate?->name_ar ?? '-',
                'daily' => $dailyPct,
                'weekly' => $weeklyPct,
                'monthly' => $monthlyPct,
                'total' => $totalPct,
                'status' => $period->is_month_locked ? 'مقفول' : 'مفتوح',
            ];
        }

        // ✅ Ranking by total
        usort($this->reportRows, fn ($a, $b) => $b['total'] <=> $a['total']);
    }
}
