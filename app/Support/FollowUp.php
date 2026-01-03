<?php

namespace App\Support;

final class FollowUp
{
    // Frequency
    public const FREQ_DAILY   = 1;
    public const FREQ_WEEKLY  = 2;
    public const FREQ_MONTHLY = 3;

    public static function frequencyLabels(): array
    {
        return [
            self::FREQ_DAILY   => 'يومي',
            self::FREQ_WEEKLY  => 'أسبوعي',
            self::FREQ_MONTHLY => 'شهري',
        ];
    }

    // Entry value
    public const VALUE_UNKNOWN = null; // not stored (DB NULL)
    public const VALUE_NO      = 0;
    public const VALUE_YES     = 1;

    public static function valueLabels(): array
    {
        return [
            self::VALUE_NO  => 'لم يتم',
            self::VALUE_YES => 'تم',
        ];
    }
}
