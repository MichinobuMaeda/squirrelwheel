<?php

namespace Tests\Feature;

use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\Models\Template;
use App\Repositories\ArticleRepository;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test list().
     *
     * @return void
     */
    public function test_list()
    {
        Article::create([
            'priority' => 0,
            'content' => 'Content 1',
            'reserved_at' => new DateTime(),
        ])->save();

        Article::create([
            'priority' => 1,
            'content' => 'Content 2',
            'reserved_at' => new DateTime(),
        ])->save();

        Article::create([
            'priority' => 2,
            'content' => 'Content 3',
            'reserved_at' => new DateTime(),
        ])->save();

        Article::create([
            'priority' => 2,
            'content' => 'Content 4',
            'reserved_at' => new DateTime('2000-01-01'),
        ])->save();

        Article::create([
            'priority' => 2,
            'content' => 'Content 5',
            'reserved_at' => new DateTime('2000-01-01'),
        ])->save();

        Article::find(2)->delete();
        $posted = Article::find(4);
        $posted->posted_at = new DateTime();
        $posted->save();

        $articles = (new ArticleRepository())->list();
        $this->assertCount(3, $articles);
        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(5, $articles[1]->id);
        $this->assertEquals(3, $articles[2]->id);

        $articles = (new ArticleRepository())->list(true);
        $this->assertCount(4, $articles);
        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(2, $articles[1]->id);
        $this->assertEquals(5, $articles[2]->id);
        $this->assertEquals(3, $articles[3]->id);
    }

    /**
     * Test generate().
     *
     * @return void
     */
    public function test_generate()
    {
        $ts = new DateTime('2000-01-01');

        Category::create([
            'name' => 'Name 0',
            'update_only' => true,
            'priority' => 0,
        ]);

        $template = Template::create([
            'category_id' => 1,
            'name' => 'Name 0',
            'body' => '%%content%% - %%link%%',
            'used_at' => new DateTime(),
        ]);

        (new ArticleRepository())->generate(
            $template,
            'Content 0',
            'https://example.com/0',
            $ts
        );
        $article = Article::find(1);

        $this->assertEquals(0, $article->priority);
        $this->assertEquals('Content 0 - https://example.com/0', $article->content);
        $this->assertEquals($ts, $article->reserved_at);

        (new ArticleRepository())->generate(
            $template,
            'Content 0',
            '',
            $ts
        );
        $article = Article::find(2);

        $this->assertEquals(0, $article->priority);
        $this->assertEquals('Content 0 -', $article->content);
        $this->assertEquals($ts, $article->reserved_at);

        (new ArticleRepository())->generate(
            $template,
            '',
            'https://example.com/0',
            $ts
        );
        $article = Article::find(3);

        $this->assertEquals(0, $article->priority);
        $this->assertEquals('- https://example.com/0', $article->content);
        $this->assertEquals($ts, $article->reserved_at);

        (new ArticleRepository())->generate(
            $template
        );
        $article = Article::find(4);

        $this->assertEquals(0, $article->priority);
        $this->assertEquals('-', $article->content);
        $this->assertIsObject($article->reserved_at);
    }
}
