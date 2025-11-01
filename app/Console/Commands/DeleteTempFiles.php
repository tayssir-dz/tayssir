<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all files of the temp directory in storage/app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        $files = $disk->files('temp');

        if (empty($files)) {
            $this->info('No files found in temp directory.');
            return self::SUCCESS;
        }

        foreach ($files as $file) {
            $disk->delete($file);
            $this->line("Deleted: {$file}");
        }

        $this->info("Successfully deleted " . count($files) . " file(s) from temp directory.");
        return self::SUCCESS;
    }
}
