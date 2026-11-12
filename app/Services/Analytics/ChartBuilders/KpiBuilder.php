<?php
// app/Services/Analytics/ChartBuilders/KpiBuilder.php

namespace App\Services\Analytics\ChartBuilders;

class KpiBuilder
{
    public static function card(string $key, string $title, $value, string $unit = ''): array
    {
        return compact('key', 'title', 'value', 'unit');
    }
}