<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use SoftDeletes;

    protected $table = 'projects';

    protected $guarded = ['id'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ModuleImportBatch::class, 'module_import_batch_id');
    }
}
