<?php

namespace App\Jobs;

use App\Models\Postcode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;

class ProcessPostcodeDownloadChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const CHUNK_SIZE = 5000;
    private const TRANSACTION_RETRY_COUNT = 10;

    /**
     * Create a new job instance.
     */
    public function __construct($file)
    {
        ini_set('memory_limit', '-1');

        $postcodes = Reader::createFromPath($file);
        $postcodes->setHeaderOffset(0);

        $postcodesCollection = LazyCollection::make($postcodes->getRecords())
            ->chunk(self::CHUNK_SIZE)
            ->each(function ($value) {
                DB::transaction(function () use ($value) {
                    collect($value)->each(function ($row, $value) {
                        Postcode::create([
                            'postcode' => $row['pcd'],
                            'lat' => $row['lat'],
                            'long' => $row['long'],
                        ]);
                    });
                }, self::TRANSACTION_RETRY_COUNT);

                Log::info('Processed a chunk...');
            });
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
