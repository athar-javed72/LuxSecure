<?php

namespace App\Support;

use App\Models\Currency;
use NumberFormatter;

class Money
{
    public static function format(float|string $amount, ?string $currencyCode = null, ?string $locale = null): string
    {
        $code = strtoupper($currencyCode ?: (string) session('currency', 'PKR'));
        $locale = $locale ?: (string) session('locale', app()->getLocale() ?: 'en');
        $amount = (float) $amount;

        $currency = Currency::query()->where('code', $code)->first();
        $decimals = $currency?->decimals ?? 2;
        $symbol = $currency?->symbol ?? $code;

        if (class_exists(NumberFormatter::class)) {
            try {
                $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
                $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
                $formatted = $fmt->formatCurrency($amount, $code);
                if ($formatted !== false) {
                    return $formatted;
                }
            } catch (\Throwable) {
                // fall through
            }
        }

        return trim($symbol.' '.number_format($amount, $decimals, '.', ','));
    }

    public static function code(?string $currencyCode = null): string
    {
        return strtoupper($currencyCode ?: (string) session('currency', 'PKR'));
    }

    /**
     * Compact listing amount: "PKR 15,000" (no trailing .00), currency code first.
     */
    public static function formatCompact(float|string $amount, ?string $currencyCode = null): string
    {
        $code = static::code($currencyCode);
        $amount = (float) $amount;
        $formatted = number_format($amount, 2, '.', ',');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $code.' '.$formatted;
    }
}
