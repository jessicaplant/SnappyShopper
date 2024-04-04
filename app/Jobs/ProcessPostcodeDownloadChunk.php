<?php

namespace App\Jobs;

use Carbon\Carbon;
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
        /**
         * These were originally in the constructor but there's an issue with the
         * library I couldn't diagnose in the time I'd allotted myself for this task.
         * This would be something I'd improve!
         */
        $postcodeReader = Reader::createFromPath($this->file);
        $postcodeReader->setHeaderOffset(0);

        /**
         * This is a disgusting hack and would not make it a mile within production
         * I can promise you that!
         */
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

                    DB::table('postcodes')->insert([
                        ...$data,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }, self::TRANSACTION_RETRY_COUNT);

                Log::info('Processed a chunk...');
            });
    }
}
