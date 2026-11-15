<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json([
                'query'   => $q,
                'groups'  => [],
                'total'   => 0,
            ]);
        }

        $like = '%' . $q . '%';
        $groups = [];
        $total = 0;

        // 1) Modules (from config)
        $moduleHits = [];
        foreach (config('excel_modules', []) as $code => $cfg) {
            $name = (string) ($cfg['name_ar'] ?? $code);
            $table = (string) ($cfg['table'] ?? '');
            $hay = mb_strtolower($code . ' ' . $name . ' ' . $table);
            if (str_contains($hay, mb_strtolower($q))) {
                $moduleHits[] = [
                    'title'    => $name,
                    'subtitle' => $code . ($table ? ' · ' . $table : ''),
                    'url'      => route('admin.excel-modules.table', $code),
                    'icon'     => 'bi-table',
                ];
            }
        }
        if ($moduleHits) {
            $groups[] = [
                'label'   => 'الموديولات',
                'key'     => 'modules',
                'results' => array_slice($moduleHits, 0, 8),
            ];
            $total += count($moduleHits);
        }

        // 2) Records inside each module table
        $recordHits = [];
        foreach (config('excel_modules', []) as $code => $cfg) {
            $modelClass = $cfg['model'] ?? null;
            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }
            try {
                $table = (new $modelClass)->getTable();
                if (!Schema::hasTable($table)) {
                    continue;
                }
                $searchCols = array_values(array_filter([
                    'name', 'name_ar', 'name_en', 'site_name', 'warehouse_name',
                    'facility_name', 'product_name', 'company_name', 'unit_name',
                    'unit_code', 'governorate', 'owner_organization', 'address',
                    'operating_status', 'readiness_level',
                ], fn ($c) => Schema::hasColumn($table, $c)));

                if (!$searchCols) {
                    continue;
                }

                $query = $modelClass::query();
                $query->where(function ($qq) use ($searchCols, $like) {
                    foreach ($searchCols as $col) {
                        $qq->orWhere($col, 'like', $like);
                    }
                });

                $rows = $query->orderByDesc('id')->limit(6)->get();
                foreach ($rows as $row) {
                    $title = $row->name
                        ?? $row->name_ar
                        ?? $row->site_name
                        ?? $row->warehouse_name
                        ?? $row->facility_name
                        ?? $row->product_name
                        ?? $row->unit_name
                        ?? $row->company_name
                        ?? ('#' . $row->id);

                    $subtitleParts = array_filter([
                        $cfg['name_ar'] ?? $code,
                        $row->unit_name ?? null,
                        $row->governorate ?? null,
                    ]);

                    $recordHits[] = [
                        'title'    => (string) $title,
                        'subtitle' => implode(' · ', $subtitleParts),
                        'url'      => route('admin.excel-modules.table.record', [$code, $row->id]),
                        'icon'     => 'bi-file-earmark-text',
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        if ($recordHits) {
            $groups[] = [
                'label'   => 'السجلات',
                'key'     => 'records',
                'results' => array_slice($recordHits, 0, 12),
            ];
            $total += count($recordHits);
        }

        // 3) Users
        try {
            $users = User::query()
                ->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                    if (Schema::hasColumn('users', 'phone')) {
                        $qq->orWhere('phone', 'like', $like);
                    }
                })
                ->limit(6)
                ->get(['id', 'name', 'email']);

            if ($users->isNotEmpty()) {
                $userHits = $users->map(fn ($u) => [
                    'title'    => $u->name,
                    'subtitle' => $u->email,
                    'url'      => route('admin.users.show', $u->id),
                    'icon'     => 'bi-person',
                ])->all();

                $groups[] = [
                    'label'   => 'المستخدمون',
                    'key'     => 'users',
                    'results' => $userHits,
                ];
                $total += count($userHits);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // 4) Units (if model exists)
        try {
            if (class_exists(\App\Models\Unit::class)) {
                $unitModel = \App\Models\Unit::class;
                $unitTable = (new $unitModel)->getTable();
                $uq = $unitModel::query();
                $uq->where(function ($qq) use ($like, $unitTable) {
                    foreach (['name', 'name_ar', 'code'] as $col) {
                        if (Schema::hasColumn($unitTable, $col)) {
                            $qq->orWhere($col, 'like', $like);
                        }
                    }
                });
                $units = $uq->limit(6)->get();
                if ($units->isNotEmpty()) {
                    $unitHits = $units->map(function ($u) {
                        $title = $u->name ?? $u->name_ar ?? $u->code ?? ('#' . $u->id);
                        return [
                            'title'    => (string) $title,
                            'subtitle' => (string) ($u->code ?? 'Unit'),
                            'url'      => route('admin.units.show', $u->id),
                            'icon'     => 'bi-building',
                        ];
                    })->all();
                    $groups[] = [
                        'label'   => 'الوحدات',
                        'key'     => 'units',
                        'results' => $unitHits,
                    ];
                    $total += count($unitHits);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return response()->json([
            'query'  => $q,
            'groups' => $groups,
            'total'  => $total,
        ]);
    }
}
