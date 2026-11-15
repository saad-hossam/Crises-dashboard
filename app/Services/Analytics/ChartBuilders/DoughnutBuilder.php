<?php
// app/Services/Analytics/ChartBuilders/DoughnutBuilder.php

namespace App\Services\Analytics\ChartBuilders;

class DoughnutBuilder
{
    /**
     * @param array $rows  [['label' => '...', 'value' => 10], ...]
     */
    public static function build(string $field, string $title, array $rows): array
    {
        // تجميع
        $grouped = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r['label'] ?? ''));
            if ($label === '' || $label === 'unknown') {
                $label = 'غير محدد';
            }
            $grouped[$label] = ($grouped[$label] ?? 0) + (float) ($r['value'] ?? 1);
        }
        arsort($grouped);

        return [
            'type'   => 'doughnut',
            'field'  => $field,
            'title'  => $title,
            'labels' => array_keys($grouped),
            'data'   => array_values($grouped),
        ];
    }
}