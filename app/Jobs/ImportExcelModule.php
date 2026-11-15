<?php

namespace App\Jobs;

use App\Services\TypedExcelImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportExcelModule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;   // 15 دقيقة
    public int $tries   = 1;

    public function __construct(
        public string $storedPath,
        public string $originalName,
        public int $userId,
        public bool $replaceExisting = true
    ) {}

    public function handle(TypedExcelImporter $importer): void
    {
        $importer->import(
            storedPath:       $this->storedPath,
            originalFilename: $this->originalName,
            userId:           $this->userId,
            replaceExisting:  $this->replaceExisting
        );
    }
}