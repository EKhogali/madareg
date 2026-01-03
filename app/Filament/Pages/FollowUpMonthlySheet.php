<?php

namespace App\Filament\Pages;

use App\Models\Subscriber;
use App\Models\FollowUpPeriod;
use App\Models\FollowUpTemplate;
use App\Models\FollowUpItem;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

use App\Models\FollowUpEntry;
use Filament\Notifications\Notification;



class FollowUpMonthlySheet extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'المتابعة الشهرية';
    protected static ?string $navigationGroup = 'المتابعة';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.follow-up-monthly-sheet';

    /**
     * Filters / controls for the sheet
     */
    public ?int $subscriberId = null;
    public int $year;
    public int $month;

    public int $mobileDay = 1;
    public int $mobileWeek = 1;


    /**
     * Loaded period + items
     */
    public ?FollowUpPeriod $period = null;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $dailyItems;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $weeklyItems;

    /** @var \Illuminate\Support\Collection<int, FollowUpItem> */
    public $monthlyItems;

    public array $state = [
        'daily' => [],
        'weekly' => [],
        'monthly' => [],
    ];

  

    

    public function getTitle(): string
    {
        return 'المتابعة الشهرية';
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


    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        // Default to current month
        $now = now();
        $this->year = (int) $now->year;
        $this->month = (int) $now->month;

        $this->mobileDay = (int) now()->day;
        $this->mobileWeek = (int) ceil($this->mobileDay / 7);


        // Default subscriber selection: first "my subscriber"
        $firstSubscriberId = Subscriber::query()
            ->where('user_id', Auth::id()) // ✅ ONLY my subscribers
            ->orderBy('id')
            ->value('id');

        $this->subscriberId = $firstSubscriberId;

        // Load initial data if we have a subscriber
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
                            // ->searchable()
                            ->preload()
                            ->options(
                                fn() => Subscriber::query()
                                    ->where('user_id', Auth::id()) // ✅ ONLY my subscribers
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
        // Adjust the range to your needs:
        $current = (int) now()->year;

        return collect(range($current - 1, $current + 1))
            ->mapWithKeys(fn($y) => [$y => (string) $y])
            ->toArray();
    }

    /**
     * Critical: Users can only access if logged in & active
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user !== null && (int) $user->status === 1;
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

        // ✅ Enforce ownership at runtime (even if someone tries to hack the ID)
        $subscriber = Subscriber::query()
            ->where('id', $this->subscriberId)
            ->where('user_id', Auth::id()) // ✅ ONLY my subscribers
            ->firstOrFail();

        // Subscriber must have template assigned
        $templateId = $subscriber->follow_up_template_id;

        if (!$templateId) {
            // You can show a friendly message in UI later
            return;
        }

        // Create or load the period
        $this->period = FollowUpPeriod::query()->firstOrCreate(
            [
                'subscriber_id' => $subscriber->id,
                'year' => $this->year,
                'month' => $this->month,
            ],
            [
                'follow_up_template_id' => $templateId,
                'user_id' => Auth::id(), // parent user
                'is_month_locked' => false,
            ]
        );

        // Safety: if period belongs to another user for any reason => forbid
        if ((int) $this->period->user_id !== (int) Auth::id()) {
            abort(403);
        }

        // Load items for this template
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





    protected function loadStateFromDatabase(): void
    {
        if (!$this->period) {
            $this->state = ['daily' => [], 'weekly' => [], 'monthly' => []];
            return;
        }

        $daysInMonth = $this->daysInMonth();

        // Initialize defaults: all unchecked
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

        // Load existing entries (done = 1)
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

        // Save DAILY
        foreach ($this->state['daily'] as $day => $items) {
            $day = (int) $day;

            $date = Carbon::createFromDate($this->year, $this->month, $day)->toDateString();
            $weekIndex = (int) ceil($day / 7);

            // Week locked => skip
            if ($this->period->isWeekLocked($weekIndex)) {
                continue;
            }

            foreach ($items as $itemId => $checked) {
                $checked = (bool) $checked;

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
                    // unchecked => delete the entry (unknown)
                    FollowUpEntry::query()
                        ->where('follow_up_period_id', $this->period->id)
                        ->where('follow_up_item_id', $itemId)
                        ->whereDate('date', $date)
                        ->delete();
                }
            }
        }

        // Save WEEKLY
        foreach ($this->state['weekly'] as $weekIndex => $items) {
            $weekIndex = (int) $weekIndex;

            if ($this->period->isWeekLocked($weekIndex)) {
                continue;
            }

            foreach ($items as $itemId => $checked) {
                $checked = (bool) $checked;

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

        // Save MONTHLY (month lock already checked)
        foreach ($this->state['monthly'] as $itemId => $checked) {
            $checked = (bool) $checked;

            if ($checked) {
                FollowUpEntry::updateOrCreate(
                    [
                        'follow_up_period_id' => $this->period->id,
                        'follow_up_item_id' => $itemId,
                        'week_index' => null,
                        'date' => null,
                    ],
                    [
                        'value' => 1,
                    ]
                );
            } else {
                FollowUpEntry::query()
                    ->where('follow_up_period_id', $this->period->id)
                    ->where('follow_up_item_id', $itemId)
                    ->whereNull('date')
                    ->whereNull('week_index')
                    ->delete();
            }
        }

        Notification::make()
            ->title('تم الحفظ ✅')
            ->success()
            ->send();

        // Reload from DB to stay consistent
        $this->loadPeriodAndItems();
        $this->loadStateFromDatabase();
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





    public function lockMonth(): void
    {
        if (!$this->period) {
            return;
        }

        if ($this->period->is_month_locked) {
            return;
        }

        $this->period->update([
            'is_month_locked' => true,
            'month_locked_at' => now(),
            'month_locked_by' => auth()->id(),
        ]);

        Notification::make()->title('تم قفل الشهر ✅')->success()->send();
        $this->loadPeriodAndItems();
    }

    public function unlockMonth(): void
    {
        if (!$this->period) {
            return;
        }

        if (!$this->period->is_month_locked) {
            return;
        }

        $this->period->update([
            'is_month_locked' => false,
            'month_locked_at' => null,
            'month_locked_by' => null,
        ]);

        Notification::make()->title('تم فتح الشهر ✅')->success()->send();
        $this->loadPeriodAndItems();
    }

    public function lockWeek(int $weekIndex): void
    {
        if (!$this->period) {
            return;
        }

        if ($this->period->is_month_locked) {
            Notification::make()->title('الشهر مقفول')->danger()->send();
            return;
        }

        $weekIndex = max(1, min(5, (int) $weekIndex));

        $this->period->weekLocks()->updateOrCreate(
            ['week_index' => $weekIndex],
            [
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => auth()->id(),
            ]
        );

        Notification::make()->title("تم قفل الأسبوع رقم {$weekIndex} ✅")->success()->send();
        $this->loadPeriodAndItems();
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





    public function lockCurrentWeek(): void
    {
        $weekIndex = (int) ceil($this->mobileDay / 7);
        $this->lockWeek($weekIndex);
    }

    public function unlockCurrentWeek(): void
    {
        $weekIndex = (int) ceil($this->mobileDay / 7);
        $this->unlockWeek($weekIndex);
    }


    public function unlockWeek(int $weekIndex): void
    {
        if (!$this->period) {
            return;
        }

        if ($this->period->is_month_locked) {
            Notification::make()->title('الشهر مقفول')->danger()->send();
            return;
        }

        $weekIndex = max(1, min(5, (int) $weekIndex));

        $this->period->weekLocks()->updateOrCreate(
            ['week_index' => $weekIndex],
            [
                'is_locked' => false,
                'locked_at' => null,
                'locked_by' => null,
            ]
        );

        Notification::make()->title("تم فتح الأسبوع رقم {$weekIndex} ✅")->success()->send();
        $this->loadPeriodAndItems();
    }
}
