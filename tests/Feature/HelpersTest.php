<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use DateTime;
use App\Models\Article;
use App\Models\Category;
use App\Models\Template;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listCategories().
     *
     * @return void
     */
    public function test_listCategories()
    {
        Category::create([
            'name' => 'Name 0',
            'update_only' => true,
            'priority' => 0,
        ])->save();

        Category::create([
            'name' => 'Name 1',
            'update_only' => false,
            'priority' => 1,
        ])->save();

        Category::create([
            'name' => 'Name 2',
            'feed' => 'https://example.com/feed.xml',
            'update_only' => true,
            'priority' => 1,
        ])->save();

        Category::create([
            'name' => 'Name 3',
            'update_only' => false,
            'priority' => 1,
        ])->save();

        Category::find(3)->delete();

        $categories = listCategories();
        $this->assertCount(3, $categories);
        $this->assertEquals(1, $categories[0]->id);
        $this->assertEquals(2, $categories[1]->id);
        $this->assertEquals(4, $categories[2]->id);

        $categories = listCategories(true);
        $this->assertCount(4, $categories);
        $this->assertEquals(1, $categories[0]->id);
        $this->assertEquals(2, $categories[1]->id);
        $this->assertEquals(3, $categories[2]->id);
        $this->assertEquals(4, $categories[3]->id);
    }

    /**
     * Test listTemplates().
     *
     * @return void
     */
    public function test_listTemplates()
    {
        Category::create([
            'name' => 'Name 0',
            'update_only' => true,
            'priority' => 0,
        ])->save();

        Category::create([
            'name' => 'Name 1',
            'update_only' => false,
            'priority' => 1,
        ])->save();

        Category::create([
            'name' => 'Name 2',
            'update_only' => false,
            'priority' => 1,
        ])->save();

        Template::create([
            'category_id' => 1,
            'name' => 'Name 0',
            'body' => 'Body 0',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => 2,
            'name' => 'Name 1',
            'body' => 'Body 1',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => 2,
            'name' => 'Name 2',
            'body' => 'Body 2',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => 2,
            'name' => 'Name 3',
            'body' => 'Body 3',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => 2,
            'name' => 'Name 4',
            'body' => 'Body 4',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => 3,
            'name' => 'Name 5',
            'body' => 'Body 5',
            'used_at' => new DateTime,
        ])->save();

        Template::find(4)->delete();

        $templates = listTemplates();
        $this->assertCount(5, $templates);
        $this->assertEquals(1, $templates[0]->id);
        $this->assertEquals(2, $templates[1]->id);
        $this->assertEquals(3, $templates[2]->id);
        $this->assertEquals(5, $templates[3]->id);
        $this->assertEquals(6, $templates[4]->id);

        $templates = listTemplates(true);
        $this->assertCount(6, $templates);
        $this->assertEquals(1, $templates[0]->id);
        $this->assertEquals(2, $templates[1]->id);
        $this->assertEquals(3, $templates[2]->id);
        $this->assertEquals(4, $templates[3]->id);
        $this->assertEquals(5, $templates[4]->id);
        $this->assertEquals(6, $templates[5]->id);
    }

    /**
     * Test listArticles().
     *
     * @return void
     */
    public function test_listArticles()
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

        $articles = listArticles();
        $this->assertCount(3, $articles);
        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(5, $articles[1]->id);
        $this->assertEquals(3, $articles[2]->id);

        $articles = listArticles(true);
        $this->assertCount(4, $articles);
        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(2, $articles[1]->id);
        $this->assertEquals(5, $articles[2]->id);
        $this->assertEquals(3, $articles[3]->id);
    }

    /**
     * Test generateArticle().
     *
     * @return void
     */
    public function test_generateArticle()
    {
        $ts = new DateTime('2000-01-01');

        Category::create([
            'name' => 'Name 0',
            'update_only' => true,
            'priority' => 0,
        ])->save();

        Category::create([
            'name' => 'Name 1',
            'update_only' => false,
            'priority' => 1,
        ])->save();

        Template::create([
            'category_id' => 1,
            'name' => 'Name 0',
            'body' => '%%content%% - %%link%%',
            'used_at' => new DateTime(),
        ])->save();

        Template::create([
            'category_id' => 2,
            'name' => 'Name 1',
            'body' => '%%content%%',
            'used_at' => new DateTime,
        ])->save();

        $article = generateArticle([
            'template_id' => 1,
            'content' => 'Content 0',
            'link' => 'https://example.com/0',
            'reserved_at' => $ts->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals(0, $article['priority']);
        $this->assertEquals('Content 0 - https://example.com/0', $article['content']);
        $this->assertEquals($ts, $article['reserved_at']);

        $article = generateArticle([
            'template_id' => 1,
            'content' => 'Content 0',
            'link' => null,
            'reserved_at' => $ts->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals(0, $article['priority']);
        $this->assertEquals('Content 0 - ', $article['content']);
        $this->assertEquals($ts, $article['reserved_at']);

        $article = generateArticle([
            'template_id' => 1,
            'content' => null,
            'link' => 'https://example.com/0',
            'reserved_at' => $ts->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals(0, $article['priority']);
        $this->assertEquals(' - https://example.com/0', $article['content']);
        $this->assertEquals($ts, $article['reserved_at']);

        $article = generateArticle([
            'template_id' => 1,
            'content' => 'Content 0',
            'reserved_at' => $ts->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals(0, $article['priority']);
        $this->assertEquals('Content 0 - ', $article['content']);
        $this->assertEquals($ts, $article['reserved_at']);

        $article = generateArticle([
            'template_id' => 1,
            'link' => 'https://example.com/0',
            'reserved_at' => $ts->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals(0, $article['priority']);
        $this->assertEquals(' - https://example.com/0', $article['content']);
        $this->assertEquals($ts, $article['reserved_at']);
    }
}
