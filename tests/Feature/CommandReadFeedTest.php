<?php

namespace Tests\Feature;

use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Console\Commands\ReadFeed;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FeedRepository;

class CommandReadFeedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the category which is update only.
     *
     * @return void
     */
    public function test_updateOnly()
    {
        $category = Category::create([
            'name' => 'Update Only',
            'feed' => 'https://example.com/?feed=cat5',
            'update_only' => true,
            'priority' => 2,
            'checked_at' => new DateTime('2022-12-20T00:00:00.000Z'),
        ]);

        $category->templates()->create([
            'name' => 'Update Only 1',
            'body' => <<<END
Description 1
END,
            'used_at' => new DateTime('2000/01/01'),
        ]);

        $category->templates()->create([
            'name' => 'Update Only 2',
            'body' => <<<END
Description 2
END,
            'used_at' => new DateTime('2000/01/01'),
        ]);

        Http::fake([
            'https://example.com/*' => Http::response(
                file_get_contents(__DIR__ . '/data/sample_cat5.xml'),
                200,
                []
            ),
        ]);

        (new ReadFeed())->handle(
            new ArticleRepository(),
            new CategoryRepository(),
            new FeedRepository(),
        );

        $articles = Article::orderBy('id')->get();
        $this->assertCount(1, $articles);

        $article = $articles[0];
        $this->assertEquals(2, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ 1/', $article->content);

        (new ReadFeed())->handle(
            new ArticleRepository(),
            new CategoryRepository(),
            new FeedRepository(),
        );
        $articles = Article::orderBy('id')->get();
        $this->assertCount(1, $articles);
    }

    /**
     * Test the category which is not update only.
     *
     * @return void
     */
    public function test_notUpdateOnly()
    {
        $category = Category::create([
            'name' => 'Articles',
            'feed' => 'https://example.com/?feed=cat10',
            'update_only' => false,
            'priority' => 3,
            'checked_at' => new DateTime('2022-12-20T00:00:00.000Z'),
        ]);

        $template = $category->templates()->create([
            'name' => 'Articles 1',
            'body' => <<<END
Description 1
%%content%%
%%link%%
END,
            'used_at' => new DateTime('2000/01/01'),
        ]);

        $template = $category->templates()->create([
            'name' => 'Articles 2',
            'body' => <<<END
Description 2
%%content%%
%%link%%
END,
            'used_at' => new DateTime('2000/01/02'),
        ]);

        Http::fake([
            'https://example.com/*' => Http::response(
                file_get_contents(__DIR__ . '/data/sample_cat10.xml'),
                200,
                []
            ),
        ]);

        (new ReadFeed())->handle(
            new ArticleRepository(),
            new CategoryRepository(),
            new FeedRepository(),
        );

        $articles = Article::orderBy('id')->get();
        $this->assertCount(5, $articles);

        $article = $articles[0];
        $this->assertEquals(3, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ 1/', $article->content);
        $this->assertMatchesRegularExpression('/Title\ 1/', $article->content);
        $this->assertMatchesRegularExpression('/https:\/\/example.com\/\?p=1/', $article->content);

        $article = $articles[1];
        $this->assertEquals(3, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ 2/', $article->content);
        $this->assertMatchesRegularExpression('/Title\ 2/', $article->content);
        $this->assertMatchesRegularExpression('/https:\/\/example.com\/\?p=2/', $article->content);

        $article = $articles[2];
        $this->assertEquals(3, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ /', $article->content);
        $this->assertMatchesRegularExpression('/Title\ 3/', $article->content);
        $this->assertMatchesRegularExpression('/https:\/\/example.com\/\?p=3/', $article->content);

        $article = $articles[3];
        $this->assertEquals(3, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ /', $article->content);
        $this->assertMatchesRegularExpression('/Title\ 4/', $article->content);
        $this->assertMatchesRegularExpression('/https:\/\/example.com\/\?p=4/', $article->content);

        $article = $articles[4];
        $this->assertEquals(3, $article->priority);
        $this->assertMatchesRegularExpression('/^Description\ /', $article->content);
        $this->assertMatchesRegularExpression('/Title\ 8/', $article->content);
        $this->assertMatchesRegularExpression('/https:\/\/example.com\/\?p=8/', $article->content);

        (new ReadFeed())->handle(
            new ArticleRepository(),
            new CategoryRepository(),
            new FeedRepository(),
        );

        $articles = Article::orderBy('id')->get();
        $this->assertCount(5, $articles);
    }
}
