<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPostcodeDownloadChunk;
use App\Models\Postcode;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class PullPostcodeDataCommand extends Command
{
    private const URL = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';
    private const FILENAME = 'postcodes.zip';
    private const EXTRACTION_PATH = 'postcodes';
    private const EXTRACTED_MULTI_CSV_PATH = 'postcodes/Data/multi_csv';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pull-postcode-data-command {--truncate-postcodes=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update local postcode data';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        /**
         * Here I'd like to do some checks of some sort to ensure the file hasn't been updated
         * on the remote end
         */
        if (!file_exists(storage_path(self::FILENAME))) {
            $this->info('Unable to find the local file - downloading! Please be patient...');

            /**
             * This would be an environment variable for the remote location to make updating it
             * easier but... this is quicker for now
             */
            Http::sink(storage_path(self::FILENAME))->get(self::URL);
        }

        $this->info('Preparing unpacking of Zip file...');

        $zip = new ZipArchive();

        if ($zip->open(storage_path(self::FILENAME))) {
//            $zip->extractTo(storage_path(self::EXTRACTION_PATH));

            $files = File::files(storage_path(self::EXTRACTED_MULTI_CSV_PATH));

            Postcode::truncate();

            /**
             * I decided to use the multi-csv option they give you as many smaller jobs in transactions feels
             * more robust than one big one
             */
            foreach ($files as $file) {
                if ($file->getExtension() === 'csv') {
                    $this->info('Dispatching a job to parse a chunk...');
                    ProcessPostcodeDownloadChunk::dispatch($file->getRealPath());
                }
            }
            return true;
        }

        $zip->close();

        /**
         * Here we need some error handling...
         */
        $this->error('Unable to work with the downloaded zip file!');

        return false;
    }
}
