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

    /** Mobile (kept, even if disabled in UI) */
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
        return auth()->check() && auth()->user()->isStaff();
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

    public function getTitle(): string
    {
        return 'المتابعة الشهرية';
    }

    /**
     * Critical: Users can only access if logged in & active
     */
    // public static function canAccess(): bool
    // {
    //     $user = Auth::user();
    //     return $user !== null && (int) $user->status === 1;
    // }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $now = now();
        $this->year = (int) $now->year;
        $this->month = (int) $now->month;

        $this->mobileDay = (int) $now->day;
        $this->mobileWeek = (int) ceil($this->mobileDay / 7);

        // Default subscriber selection: first "my subscriber"
        $firstSubscriberId = Subscriber::query()
            ->where('user_id', Auth::id())
            ->orderBy('id')
            ->value('id');

        $this->subscriberId = $firstSubscriberId;

        if ($this->subscriberId) {
            $this->loadPeriodAndItems();
        }

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
                                    ->options(
                                        fn() => Subscriber::query()
                                            ->where('user_id', Auth::id())
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
            ->mapWithKeys(fn($y) => [$y => (string) $y])
            ->toArray();
    }

    /**
     * Helpful values for Blade
     */
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

        // ✅ Enforce ownership at runtime
        $subscriber = Subscriber::query()
            ->where('id', $this->subscriberId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $templateId = $subscriber->follow_up_template_id;
        if (!$templateId) {
            return;
        }

        $this->period = FollowUpPeriod::query()->firstOrCreate(
            [
                'subscriber_id' => $subscriber->id,
                'year' => $this->year,
                'month' => $this->month,
            ],
            [
                'follow_up_template_id' => $templateId,
                'user_id' => Auth::id(),
                'is_month_locked' => false,
            ]
        );

        // Safety
        if ((int) $this->period->user_id !== (int) Auth::id()) {
            abort(403);
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

        // defaults: all unchecked
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
                // monthly
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
        if (!$this->period) {
            return;
        }

        // Month locked => block saving
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

                if ($itemId < 1)
                    continue;

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

                if ($itemId < 1)
                    continue;

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
        // Save MONTHLY (safe without DB unique)
        // =========================
        foreach ($this->state['monthly'] as $itemId => $checked) {
            $itemId = (int) $itemId;
            $checked = (bool) $checked;

            if ($itemId < 1)
                continue;

            $query = FollowUpEntry::query()
                ->where('follow_up_period_id', $this->period->id)
                ->where('follow_up_item_id', $itemId)
                ->whereNull('date')
                ->whereNull('week_index');

            if ($checked) {
                // Defensive: ensure only 1 row exists
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

        // Reload to stay consistent
        $this->loadPeriodAndItems();
    }


    /**
     * ✅ Autosave hook: every checkbox change persists immediately
     */
    public function updatedState($value, string $key): void
    {
        //     if (!$this->period) {
        //         return;
        //     }

        //     // Month locked => block update (revert)
        //     if ($this->period->is_month_locked) {
        //         data_set($this->state, $key, !$value);
        //         Notification::make()->title('الشهر مقفول')->danger()->send();
        //         return;
        //     }

        //     $parts = explode('.', $key);
        //     $type = $parts[0] ?? null;

        //     if ($type === 'daily') {
        //         $day = (int) ($parts[1] ?? 0);
        //         $itemId = (int) ($parts[2] ?? 0);

        //         if ($day < 1 || $day > $this->daysInMonth() || $itemId < 1) {
        //             return;
        //         }

        //         $weekIndex = (int) ceil($day / 7);

        //         if ($this->period->isWeekLocked($weekIndex)) {
        //             data_set($this->state, $key, !$value);
        //             Notification::make()->title("الأسبوع رقم {$weekIndex} مقفول")->danger()->send();
        //             return;
        //         }

        //         $date = Carbon::createFromDate($this->year, $this->month, $day)->toDateString();

        //         $this->upsertDailyEntry($itemId, (bool) $value, $date);

        //         return;
        //     }

        //     if ($type === 'weekly') {
        //         $weekIndex = (int) ($parts[1] ?? 0);
        //         $itemId = (int) ($parts[2] ?? 0);

        //         if ($weekIndex < 1 || $weekIndex > 5 || $itemId < 1) {
        //             return;
        //         }

        //         if ($this->period->isWeekLocked($weekIndex)) {
        //             data_set($this->state, $key, !$value);
        //             Notification::make()->title("الأسبوع رقم {$weekIndex} مقفول")->danger()->send();
        //             return;
        //         }

        //         $this->upsertWeeklyEntry($itemId, (bool) $value, $weekIndex);

        //         return;
        //     }

        //     if ($type === 'monthly') {
        //         $itemId = (int) ($parts[1] ?? 0);

        //         if ($itemId < 1) {
        //             return;
        //         }

        //         $this->upsertMonthlyEntry($itemId, (bool) $value);

        //         return;
        //     }
    }

    protected function upsertDailyEntry(int $itemId, bool $checked, string $date): void
    {
        if (!$this->period)
            return;

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

    protected function upsertWeeklyEntry(int $itemId, bool $checked, int $weekIndex): void
    {
        if (!$this->period)
            return;

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

    /**
     * ✅ Defensive monthly upsert (no unique index in DB)
     */
    protected function upsertMonthlyEntry(int $itemId, bool $checked): void
    {
        if (!$this->period)
            return;

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

    /**
     * ✅ Unified locking: month/week/day (day maps to week)
     *
     * scope:
     * - month: index ignored
     * - week: index = 1..5
     * - day: index = day number (1..31) -> week index derived
     */
    public function toggleLock(string $scope, ?int $index = null, bool $locked = true): void
    {
        if (!$this->period)
            return;

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

        if ($this->mobileDay < 1) {
            $this->mobileDay = 1;
        }

        if ($this->mobileDay > $days) {
            $this->mobileDay = $days;
        }

        $this->mobileWeek = (int) ceil($this->mobileDay / 7);

        if ($this->mobileWeek < 1) {
            $this->mobileWeek = 1;
        }

        if ($this->mobileWeek > 5) {
            $this->mobileWeek = 5;
        }
    }

    /** Mobile helpers (kept) */
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
