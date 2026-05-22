<?php

namespace App\Console\Commands;

use App\Jobs\GenerateThumbnail;
use App\Models\MotorcycleImage;
use Illuminate\Console\Command;

class GenerateMissingThumbnails extends Command
{
    protected $signature = 'thumbnails:generate {--dry-run : List images without dispatching jobs}';

    protected $description = 'Dispatch GenerateThumbnail jobs for all images missing a thumbnail';

    public function handle(): int
    {
        $images = MotorcycleImage::where('has_thumbnail', false)->get();

        if ($images->isEmpty()) {
            $this->info('All images already have thumbnails.');
            return self::SUCCESS;
        }

        $this->info("Found {$images->count()} image(s) without thumbnails.");

        if ($this->option('dry-run')) {
            $this->table(['ID', 'Path'], $images->map(fn ($i) => [$i->id, $i->path])->toArray());
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        foreach ($images as $image) {
            GenerateThumbnail::dispatch($image);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched {$images->count()} job(s) to the queue.");

        return self::SUCCESS;
    }
}
