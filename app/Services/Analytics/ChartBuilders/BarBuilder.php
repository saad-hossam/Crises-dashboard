<?php
// app/Services/Analytics/ChartBuilders/BarBuilder.php

namespace App\Services\Analytics\ChartBuilders;

class BarBuilder
{
    /**
     * @param array $rows      [['label' => '...', 'value' => 10], ...]
     * @param bool  $horizontal
     */
    public static function build(
        string $field,
        string $title,
        array $rows,
        bool $horizontal = false,
        int $limit = 10
    ): array {
        $grouped = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r['label'] ?? ''));
            if ($label === '' || $label === 'unknown') {
                continue;
            }
            $grouped[$label] = ($grouped[$label] ?? 0) + (float) ($r['value'] ?? 1);
        }
        arsort($grouped);
        $grouped = array_slice($grouped, 0, $limit, true);

        return [
            'type'       => $horizontal ? 'horizontalBar' : 'bar',
            'field'      => $field,
            'title'      => $title,
            'labels'     => array_keys($grouped),
            'data'       => array_values($grouped),
        ];
    }
}