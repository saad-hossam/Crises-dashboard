<?php

return [
    // Maximum upload size in kilobytes (10 MB)
    'max_file_size' => 10240,

    'allowed_extensions' => ['xlsx', 'xls'],

    'allowed_mimetypes' => [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
    ],

    'storage_disk' => 'local',
    'storage_path' => 'imports',

    // Preview page size
    'preview_rows' => 20,

    /*
    |--------------------------------------------------------------------------
    | Structural validation — CONFIGURE AFTER INSPECTING THE REAL EXCEL FILE
    |--------------------------------------------------------------------------
    |
    | These are placeholders. Do not treat them as confirmed business columns.
    | Update once the actual crisis-data workbook structure is known.
    |
    */
    'structure' => [
        'header_row' => 1,
        'expected_sheets' => [],     // e.g. ['Crises', 'Victims']
        'required_columns' => [],    // e.g. ['crisis_id', 'date', 'location']
        'optional_columns' => [],
        'column_types' => [],        // e.g. ['date' => 'date', 'count' => 'integer']
    ],
];