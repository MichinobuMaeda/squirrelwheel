<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FeedRepository;

class ReadFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:read_feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read the site\'s feed and add article posts to the queue.';

    /**
     * Get feeds and generate articles.
     *
     * @param  ArticleRepository  $articles
     * @param  CategoryRepository  $categories
     * @param  FeedRepository  $feeds
     * @return int
     */
    public function handle(
        ArticleRepository $articles,
        CategoryRepository $categories,
        FeedRepository $feeds,
    ) {
        config(['logging.default' => 'job']);
        Log::info('start: ReadFeed');

        foreach ($categories->listForFeed() as $category) {
            Log::info('get: ' . $category->feed);
            $feed = $feeds->atom(
                $category->feed,
                $category->checked_at->getTimezone(),
            );
            if (!$feed) continue;

            $checkedAt = $feed['updated'];
            if (getMilliDiff($checkedAt, $category->checked_at) <= 0) continue;

            if ($category->update_only) {
                Log::info('updated: ' . $category->name);
                $articles->generate($category->templates()->orderBy('used_at')->first());
            } else {
                foreach ($feed['entries'] as $entry) {
                    $updated = $entry['updated'];
                    if (getMilliDiff($updated, $category->checked_at) <= 0) continue;

                    Log::info('updated: ' . $entry['title']);
                    $articles->generate(
                        $category->templates()->orderBy('used_at')->first(),
                        $entry['title'],
                        $entry['link'],
                    );
                }
            }

            $category->fill(['checked_at' => $checkedAt])->save();
        }

        Log::info('end: ReadFeed');
        return Command::SUCCESS;
    }
}
