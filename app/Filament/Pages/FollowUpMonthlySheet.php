<?php

namespace App\Filament\Pages;

use App\Models\Subscriber;
use App\Models\FollowUpPeriod;
use App\Models\FollowUpItem;
use App\Models\FollowUpEntry;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Support\Traits\HasLauncherBackAction;

class FollowUpMonthlySheet extends Page
{
    use HasLauncherBackAction;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'المتابعة الشهرية';
    protected static ?string $navigationGroup = 'المتابعة';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.follow-up-monthly-sheet';

    /** Filters / controls */
    public ?int $subscriberId = null;
    public int $year;
    public int $month;

    /** Mobile */
    public int $mobileDay = 1;
    public int $mobileWeek = 1;

    /** Loaded period + items */
    public ?FollowUpPeriod $period = null;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $dailyItems;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $weeklyItems;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $monthlyItems;

    /** State */
    public array $state = [
        'daily' => [],
        'weekly' => [],
        'monthly' => [],
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array((int) $user->role, [1, 3, 4], true); // SuperAdmin, Supervisor, Parent
    }

    public function getTitle(): string
    {
        return 'المتابعة الشهرية';
    }

    /**
     * ✅ Central policy:
     * - Super Admin: all subscribers
     * - Supervisor: only subscribers in assigned groups
     * - Parent: only subscribers where subscriber.user_id = auth user
     */
    protected function subscribersForUser(): Builder
    {
        $u = Auth::user();
        $q = Subscriber::query();

        if (!$u) {
            return $q->whereRaw('1=0');
        }

        // Parent: only own subscribers
        if ((int) $u->role === 4) {
            return $q->where('user_id', $u->id);
        }

        // Supervisor: only group subscribers
        if ((int) $u->role === 3) {
            $groupIds = $u->groups()->pluck('groups.id')->toArray();
            return $q->whereIn('group_id', $groupIds);
        }

        // Super Admin: all
        return $q;
    }

    /**
     * ✅ Enforce scope server-side to prevent URL/state tampering.
     */
    protected function assertSubscriberInScope(int $subscriberId): void
    {
        $ok = $this->subscribersForUser()->where('id', $subscriberId)->exists();
        abort_unless($ok, 403);
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $now = now();
        $this->year = (int) $now->year;
        $this->month = (int) $now->month;

        $this->mobileDay = (int) $now->day;
        $this->mobileWeek = (int) ceil($this->mobileDay / 7);

        // Default subscriber selection (role-aware)
        $this->subscriberId = $this->subscribersForUser()
            ->orderBy('id')
            ->value('id');

        $this->clampMobileControls();

        // Load initial data if a subscriber exists
        if ($this->subscriberId) {
            $this->loadPeriodAndItems();
        }
    }

    public function prevDay(): void
    {
        $this->mobileDay = max(1, $this->mobileDay - 1);
        $this->clampMobileControls();
    }

    public function nextDay(): void
    {
        $this->mobileDay = min($this->daysInMonth(), $this->mobileDay + 1);
        $this->clampMobileControls();
    }

    /**
     * Filament form schema for selecting subscriber and month
     */
    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\Select::make('subscriberId')
                            ->label('المشترك')
                            ->preload()
                            ->options(fn () => $this->subscribersForUser()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function () {
                                $this->loadPeriodAndItems();
                            }),

                        Forms\Components\Select::make('month')
                            ->label('الشهر')
                            ->options($this->monthOptions())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function () {
                                $this->loadPeriodAndItems();
                            }),

                        Forms\Components\Select::make('year')
                            ->label('السنة')
                            ->options($this->yearOptions())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function () {
                                $this->loadPeriodAndItems();
                            }),
                    ]),
            ]);
    }

    public function monthOptions(): array
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

    public function yearOptions(): array
    {
        $current = (int) now()->year;

        return collect(range($current - 1, $current + 1))
            ->mapWithKeys(fn ($y) => [$y => (string) $y])
            ->toArray();
    }

    public function daysInMonth(): int
    {
        if (!$this->year || !$this->month) {
            return 30;
        }

        return Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
    }

    /**
     * Load or create period (month sheet) + load template items
     */
    protected function loadPeriodAndItems(): void
    {
        $this->period = null;
        $this->dailyItems = collect();
        $this->weeklyItems = collect();
        $this->monthlyItems = collect();

        if (!$this->subscriberId) {
            return;
        }

        $subscriberId = (int) $this->subscriberId;

        // ✅ Security: ensure this subscriber is within the current user's scope
        $this->assertSubscriberInScope($subscriberId);

        $subscriber = Subscriber::query()->findOrFail($subscriberId);

        $templateId = (int) ($subscriber->follow_up_template_id ?? 0);
        if ($templateId < 1) {
            return;
        }

        // ✅ Period owner:
        // store the subscriber's parent user_id (so ownership checks keep working)
        $this->period = FollowUpPeriod::query()->firstOrCreate(
            [
                'subscriber_id' => $subscriber->id,
                'year' => (int) $this->year,
                'month' => (int) $this->month,
            ],
            [
                'follow_up_template_id' => $templateId,
                'user_id' => (int) $subscriber->user_id, // IMPORTANT: not Auth::id()
                'is_month_locked' => false,
            ]
        );

        // ✅ Safety:
        // Parents must only access their own period.
        // Supervisors already enforced via scope, but we keep this as extra defense.
        // Super Admin can access all.
        $u = Auth::user();
        if ($u && (int) $u->role === 4) {
            abort_unless((int) $this->period->user_id === (int) $u->id, 403);
        }

        $items = FollowUpItem::query()
            ->where('follow_up_template_id', $this->period->follow_up_template_id)
            ->where('is_active', true)
            ->orderBy('frequency')
            ->orderBy('sort_order')
            ->get();

        $this->dailyItems = $items->where('frequency', 1)->values();
        $this->weeklyItems = $items->where('frequency', 2)->values();
        $this->monthlyItems = $items->where('frequency', 3)->values();

        $this->loadStateFromDatabase();
        $this->clampMobileControls();
    }

    protected function loadStateFromDatabase(): void
    {
        if (!$this->period) {
            $this->state = ['daily' => [], 'weekly' => [], 'monthly' => []];
            return;
        }

        $daysInMonth = $this->daysInMonth();

        $daily = [];
        foreach (range(1, $daysInMonth) as $day) {
            $daily[$day] = [];
            foreach ($this->dailyItems as $item) {
                $daily[$day][$item->id] = false;
            }
        }

        $weekly = [];
        foreach (range(1, 5) as $weekIndex) {
            $weekly[$weekIndex] = [];
            foreach ($this->weeklyItems as $item) {
                $weekly[$weekIndex][$item->id] = false;
            }
        }

        $monthly = [];
        foreach ($this->monthlyItems as $item) {
            $monthly[$item->id] = false;
        }

        $entries = FollowUpEntry::query()
            ->where('follow_up_period_id', $this->period->id)
            ->where('value', 1)
            ->get(['follow_up_item_id', 'date', 'week_index', 'value']);

        foreach ($entries as $entry) {
            if ($entry->date) {
                $day = (int) $entry->date->day;
                if (isset($daily[$day][$entry->follow_up_item_id])) {
                    $daily[$day][$entry->follow_up_item_id] = true;
                }
            } elseif ($entry->week_index) {
                $w = (int) $entry->week_index;
                if (isset($weekly[$w][$entry->follow_up_item_id])) {
                    $weekly[$w][$entry->follow_up_item_id] = true;
                }
            } else {
                if (isset($monthly[$entry->follow_up_item_id])) {
                    $monthly[$entry->follow_up_item_id] = true;
                }
            }
        }

        $this->state = [
            'daily' => $daily,
            'weekly' => $weekly,
            'monthly' => $monthly,
        ];
    }

    public function save(): void
    {
        if (!$this->period || !$this->subscriberId) {
            return;
        }

        // ✅ Security: ensure subscriber is still in scope before writing
        $this->assertSubscriberInScope((int) $this->subscriberId);

        // Month locked => block saving (keep your policy)
        if ($this->period->is_month_locked) {
            Notification::make()
                ->title('هذا الشهر مقفول')
                ->danger()
                ->send();
            return;
        }

        // =========================
        // Save DAILY
        // =========================
        foreach ($this->state['daily'] as $day => $items) {
            $day = (int) $day;

            if ($day < 1 || $day > $this->daysInMonth()) {
                continue;
            }

            $date = Carbon::createFromDate($this->year, $this->month, $day)->toDateString();
            $weekIndex = (int) ceil($day / 7);

            // Week locked => skip daily saves in this week
            if ($this->period->isWeekLocked($weekIndex)) {
                continue;
            }

            foreach ($items as $itemId => $checked) {
                $itemId = (int) $itemId;
                $checked = (bool) $checked;

                if ($itemId < 1) {
                    continue;
                }

                if ($checked) {
                    FollowUpEntry::updateOrCreate(
                        [
                            'follow_up_period_id' => $this->period->id,
                            'follow_up_item_id' => $itemId,
                            'date' => $date,
                        ],
                        [
                            'week_index' => null,
                            'value' => 1,
                        ]
                    );
                } else {
                    FollowUpEntry::query()
                        ->where('follow_up_period_id', $this->period->id)
                        ->where('follow_up_item_id', $itemId)
                        ->whereDate('date', $date)
                        ->delete();
                }
            }
        }

        // =========================
        // Save WEEKLY
        // =========================
        foreach ($this->state['weekly'] as $weekIndex => $items) {
            $weekIndex = (int) $weekIndex;

            if ($weekIndex < 1 || $weekIndex > 5) {
                continue;
            }

            if ($this->period->isWeekLocked($weekIndex)) {
                continue;
            }

            foreach ($items as $itemId => $checked) {
                $itemId = (int) $itemId;
                $checked = (bool) $checked;

                if ($itemId < 1) {
                    continue;
                }

                if ($checked) {
                    FollowUpEntry::updateOrCreate(
                        [
                            'follow_up_period_id' => $this->period->id,
                            'follow_up_item_id' => $itemId,
                            'week_index' => $weekIndex,
                        ],
                        [
                            'date' => null,
                            'value' => 1,
                        ]
                    );
                } else {
                    FollowUpEntry::query()
                        ->where('follow_up_period_id', $this->period->id)
                        ->where('follow_up_item_id', $itemId)
                        ->where('week_index', $weekIndex)
                        ->delete();
                }
            }
        }

        // =========================
        // Save MONTHLY
        // =========================
        foreach ($this->state['monthly'] as $itemId => $checked) {
            $itemId = (int) $itemId;
            $checked = (bool) $checked;

            if ($itemId < 1) {
                continue;
            }

            $query = FollowUpEntry::query()
                ->where('follow_up_period_id', $this->period->id)
                ->where('follow_up_item_id', $itemId)
                ->whereNull('date')
                ->whereNull('week_index');

            if ($checked) {
                $existing = $query->first();

                if ($existing) {
                    $existing->update(['value' => 1]);
                } else {
                    FollowUpEntry::create([
                        'follow_up_period_id' => $this->period->id,
                        'follow_up_item_id' => $itemId,
                        'date' => null,
                        'week_index' => null,
                        'value' => 1,
                    ]);
                }
            } else {
                $query->delete();
            }
        }

        Notification::make()
            ->title('تم الحفظ ✅')
            ->success()
            ->send();

        $this->loadPeriodAndItems();
    }

    /**
     * ✅ Unified locking: month/week/day
     * Only Super Admin + Supervisor can lock/unlock (kept).
     */
    public function toggleLock(string $scope, ?int $index = null, bool $locked = true): void
    {
        $user = auth()->user();

        if (!$user || (!$user->isSuperAdmin() && !$user->isSupervisor())) {
            Notification::make()
                ->title('ليس لديك صلاحية القفل/الفتح')
                ->danger()
                ->send();
            return;
        }

        if (!$this->period) {
            return;
        }

        if ($scope === 'month') {
            $this->period->update([
                'is_month_locked' => $locked,
                'month_locked_at' => $locked ? now() : null,
                'month_locked_by' => $locked ? auth()->id() : null,
            ]);

            Notification::make()
                ->title($locked ? 'تم قفل الشهر ✅' : 'تم فتح الشهر ✅')
                ->success()
                ->send();

            $this->loadPeriodAndItems();
            return;
        }

        if (!in_array($scope, ['week', 'day'], true)) {
            return;
        }

        if ($this->period->is_month_locked) {
            Notification::make()->title('الشهر مقفول')->danger()->send();
            return;
        }

        $weekIndex = (int) $index;

        if ($scope === 'day') {
            $day = max(1, (int) $index);
            $weekIndex = (int) ceil($day / 7);
        }

        $weekIndex = max(1, min(5, $weekIndex));

        $this->period->weekLocks()->updateOrCreate(
            ['week_index' => $weekIndex],
            [
                'is_locked' => $locked,
                'locked_at' => $locked ? now() : null,
                'locked_by' => $locked ? auth()->id() : null,
            ]
        );

        Notification::make()
            ->title($locked ? "تم قفل الأسبوع رقم {$weekIndex} ✅" : "تم فتح الأسبوع رقم {$weekIndex} ✅")
            ->success()
            ->send();

        $this->loadPeriodAndItems();
    }

    protected function clampMobileControls(): void
    {
        $days = $this->daysInMonth();

        $this->mobileDay = max(1, min($days, (int) $this->mobileDay));
        $this->mobileWeek = (int) ceil($this->mobileDay / 7);
        $this->mobileWeek = max(1, min(5, $this->mobileWeek));
    }

    public function getMobileWeekIndexProperty(): int
    {
        return (int) ceil($this->mobileDay / 7);
    }

    public function getMobileLockedProperty(): bool
    {
        if (!$this->period) {
            return true;
        }

        return $this->period->is_month_locked || $this->period->isWeekLocked($this->mobileWeekIndex);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            ...parent::getHeaderActions(),
        ];
    }
}
