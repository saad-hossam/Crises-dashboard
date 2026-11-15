<?php
// app/Services/Analytics/ValueNormalizer.php

namespace App\Services\Analytics;

class ValueNormalizer
{
    /**
     * يحوّل أي قيمة رقمية نصية إلى float أو null.
     * يتعامل مع: "1,234.5" / "12%" / "5 متر" / "لا يوجد" / "غير متاح"
     */
    public static function number($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $v = trim($value);

        // قيم نصية معروفة = null
        $nullWords = [
            'لا يوجد', 'لايوجد', 'غير متاح', 'لاينطبق', 'لا ينطبق',
            'لا توجد', 'لايوجد ', '-', '—', 'غير محدد', 'بدون', 'لا',
            'طرف الجهاز التنفيذى', 'متوقف', 'معطل', 'لا يعمل',
        ];
        foreach ($nullWords as $w) {
            if (mb_strpos($v, $w) !== false) {
                return null;
            }
        }

        // نشيل الفواصل الألفية والمسافات
        $v = str_replace([',', ' ', '٬', ' '], '', $v);

        // نسبة مئوية
        if (str_contains($v, '%')) {
            $num = (float) filter_var($v, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            return $num / 100;
        }

        // نستخرج أول رقم في النص
        if (preg_match('/-?\d+(\.\d+)?/', $v, $m)) {
            return (float) $m[0];
        }

        return null;
    }

    /**
     * يحوّل مساحة إلى متر مربع.
     * يتعامل مع: "67914 متر مربع" / "متر مربع3330" / "26000 م"
     */
    public static function areaToSqm($value): ?float
    {
        $n = self::number($value);
        if ($n === null) {
            return null;
        }

        $v = (string) $value;
        // كيلومتر مربع
        if (str_contains($v, 'كم2') || str_contains($v, 'كيلو متر مربع')) {
            return $n * 1_000_000;
        }
        // هكتار
        if (str_contains($v, 'هكتار')) {
            return $n * 10_000;
        }
        // فدان
        if (str_contains($v, 'فدان')) {
            return $n * 4200;
        }

        // الافتراضي: متر مربع (أو م)
        return $n;
    }

    /**
     * يحوّل عملة إلى [amount, currency].
     */
    public static function currency($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $v = (string) $value;
        $amount = self::number($v);
        if ($amount === null) {
            return null;
        }

        $currency = 'EGP';
        if (str_contains($v, 'دولار') || str_contains($v, '$')) {
            $currency = 'USD';
        } elseif (str_contains($v, 'يورو') || str_contains($v, '€')) {
            $currency = 'EUR';
        }

        // معاملات "مليون"
        if (str_contains($v, 'مليون')) {
            $amount *= 1_000_000;
        }

        return ['amount' => $amount, 'currency' => $currency];
    }

    /**
     * يوحّد حالة المركبة / المعدة إلى canonical.
     */
    public static function status($value): string
    {
        $v = trim((string) $value);

        $map = [
            'صالح'          => 'operational',
            'صالحة للعمل'   => 'operational',
            'تعمل'          => 'operational',
            'يعمل'          => 'operational',
            'مستمر'         => 'operational',
            'منتج'          => 'operational',
            'عاطل'          => 'broken',
            'عاطلة'         => 'broken',
            'معطلة'         => 'broken',
            'لا يعمل'       => 'broken',
            'لا تعمل'       => 'broken',
            'متوقف'         => 'broken',
            'متوقفة'        => 'broken',
            'تحت التكهين'   => 'written_off',
            'مكهنة'         => 'written_off',
            'تم التكهين'    => 'written_off',
            'بانتظار الإصلاح' => 'waiting_repair',
            'مقرر إصلاحها'  => 'repair_planned',
            'جديد بالمخازن' => 'new_stock',
            'تحت التجهيز والتشغيل التجريبي' => 'pilot',
            'تحت التجهيز'   => 'pilot',
            'جاري التنفيذ'  => 'in_progress',
            'مخطط'          => 'planned',
            'لم يبدأ'       => 'planned',
        ];

        foreach ($map as $needle => $canonical) {
            if (mb_strpos($v, $needle) !== false) {
                return $canonical;
            }
        }

        return $v === '' ? 'unknown' : 'other';
    }

    /**
     * يحوّل X/Y إلى [lat, lon] decimal.
     * - Decimal عادي: نحدد الترتيب لو X>40 (lon) و Y<40 (lat) → مقلوب
     * - DMS: "31°19'54.26" → decimal
     */
    public static function coordinate($x, $y): ?array
    {
        $xN = self::number($x);
        $yN = self::number($y);
        if ($xN === null || $yN === null) {
            return null;
        }

        // DMS؟ نعالجها في helper منفصل لو احتاجنا
        $a = $xN;
        $b = $yN;

        // مصر: lat 22-32، lon 24-37
        $looksLikeLon = fn($v) => $v >= 24 && $v <= 38;
        $looksLikeLat = fn($v) => $v >= 22 && $v <= 32;

        if ($looksLikeLon($a) && $looksLikeLat($b)) {
            return ['lat' => $b, 'lon' => $a];
        }
        if ($looksLikeLat($a) && $looksLikeLon($b)) {
            return ['lat' => $a, 'lon' => $b];
        }

        return null;
    }
}