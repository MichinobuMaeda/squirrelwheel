<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateTimeZone;
use App\Models\Article;
use App\Models\Category;
use App\Models\Template;

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
    protected $description = <<<END
Read the site's feed and add article posts to the queue.
END;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        config(['logging.default' => 'job']);

        $categories = Category::whereNotNull('feed')->orderBy('feed')->get();

        foreach ($categories as $category) {
            $this->handleCategory($category);
        }

        return Command::SUCCESS;
    }

    /**
     * Handle one category.
     *
     * @return void
     */
    public function handleCategory($category)
    {
        Log::info('get: ' . $category->feed);

        $response = Http::get($category->feed);

        if ($response->status() != 200) {
            Log::info('status: ' . $response->status());
            return;
        }

        $feed = simplexml_load_string($response->body(), null, LIBXML_NOCDATA, 'atom', true);
        $feed->registerXPATHNamespace('atom', 'http://www.w3.org/2005/Atom');
        $checkedAt = new DateTime($feed->xpath('/atom:feed/atom:updated/text()')[0]);
        $checkedAt->setTimezone($category->checked_at->getTimezone());

        if ($checkedAt->format('Y-m-d\TH:i:s.vp') <= $category->checked_at->format('Y-m-d\TH:i:s.vp')) {
            Log::info('checked: ' . $category->checked_at);
            return;
        }

        if ($category->update_only) {
            Log::channel('job')->info('updated: ' . $category->name);

            $template = Template::where('category_id', $category->id)
                ->orderBy('used_at')->first();

            $this->queueArticle(
                $template->category->priority,
                $template->body
            );

            $template->used_at = new DateTime();
            $template->save();
        } else {
            $entries = $feed->xpath('/atom:feed/atom:entry');
            $count = count($entries);

            for ($i = 0; $i < $count; ++$i) {
                $updated = new DateTime($feed->xpath('/atom:feed/atom:entry/atom:updated/text()')[$i]);

                if ((int)$updated->format('Uv') <= (int)$category->checked_at->format('Uv')) continue;

                $link = $feed->xpath('/atom:feed/atom:entry/atom:link/@href')[$i];
                $title = $feed->xpath('/atom:feed/atom:entry/atom:title/text()')[$i];
                Log::info('updated: ' . $title);

                $template = Template::where('category_id', $category->id)
                    ->orderBy('used_at')->first();

                $this->queueArticle(
                    $template->category->priority,
                    str_replace(
                        '%%link%%',
                        $link,
                        str_replace(
                            '%%content%%',
                            $title,
                            $template->body,
                        )
                    )
                );

                $template->used_at = new DateTime();
                $template->save();
            }
        }

        Log::info('save: ' . $checkedAt->format('Y-m-d\TH:i:s.vp'));

        $category->checked_at = $checkedAt;
        $category->save();
    }

    /**
     * Handle one category.
     *
     * @return void
     */
    public function queueArticle($priority, $content)
    {
        $article = Article::create([
            'priority' => $priority,
            'content' => $content,
        ]);

        // TODO:
    }
}
