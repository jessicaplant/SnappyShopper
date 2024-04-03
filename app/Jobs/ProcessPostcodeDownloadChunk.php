<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;

class ProcessPostcodeDownloadChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CHUNK_SIZE = 10000;
    private const TRANSACTION_RETRY_COUNT = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $file
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $postcodeReader = Reader::createFromPath($this->file);
        $postcodeReader->setHeaderOffset(0);

        ini_set('memory_limit', '-1');

        LazyCollection::make($postcodeReader->getRecords())
            ->chunk(self::CHUNK_SIZE)
            ->each(function ($value) {
                DB::transaction(function () use ($value) {
                    $data = collect($value)->select([
                        'pcd',
                        'lat',
                        'long',
                    ])->all();

                    DB::table('postcodes')->insert($data);
                }, self::TRANSACTION_RETRY_COUNT);

                Log::info('Processed a chunk...');
            });
    }
}
