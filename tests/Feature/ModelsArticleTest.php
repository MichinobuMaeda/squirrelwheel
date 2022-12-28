<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use DateTime;
use App\Models\Article;

class ModelsArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test create().
     *
     * @return void
     */
    public function test_create()
    {
        $reserved_at = new DateTime('2020-01-01T12:34:56.000Z');

        $articles = Article::orderBy('id')->get();
        $this->assertCount(0, $articles);

        Article::create([
            'priority' => 0,
            'content' => 'Content 1',
        ])->save();

        Article::create([
            'priority' => 1,
            'content' => 'Content 2',
            'reserved_at' => $reserved_at,
        ])->save();

        $articles = Article::orderBy('id')->get();
        $this->assertCount(2, $articles);

        $article = $articles[0];
        $this->assertEquals(1, $article->id);
        $this->assertEquals(0, $article->priority);
        $this->assertEquals('Content 1', $article->content);
        $this->assertNull($article->reserved_at);
        $this->assertNull($article->posted_at);

        $article = $articles[1];
        $this->assertEquals(2, $article->id);
        $this->assertEquals(1, $article->priority);
        $this->assertEquals('Content 2', $article->content);
        $this->assertEquals($reserved_at, $article->reserved_at);
        $this->assertNull($article->posted_at);
    }

    /**
     * Test update().
     *
     * @return void
     */
    public function test_update()
    {
        $reserved_at = new DateTime('2020-01-01T12:34:56.000Z');
        $posted_at = new DateTime('2020-12-31T12:34:56.000Z');

        Article::create([
            'priority' => 0,
            'content' => 'Content 1',
        ])->save();
        $article = Article::find(1);
        $article->fill([
            'priority' => 1,
            'content' => 'Content 2',
            'reserved_at' => $reserved_at,
        ]);
        $article->save();

        $article = Article::find(1);
        $this->assertEquals(1, $article->id);
        $this->assertEquals(1, $article->priority);
        $this->assertEquals('Content 2', $article->content);
        $this->assertEquals($reserved_at, $article->reserved_at);
        $this->assertNull($article->posted_at);

        $article->fill([
            'posted_at' => $posted_at,
        ]);
        $article->save();

        $article = Article::find(1);
        $this->assertEquals(1, $article->id);
        $this->assertEquals(1, $article->priority);
        $this->assertEquals('Content 2', $article->content);
        $this->assertEquals($reserved_at, $article->reserved_at);
        $this->assertEquals($posted_at, $article->posted_at);
    }

    /**
     * Test delete().
     *
     * @return void
     */
    public function test_delete()
    {
        $articles = Article::orderBy('id')->get();
        $this->assertCount(0, $articles);

        Article::create([
            'priority' => 0,
            'content' => 'Content 1',
        ])->save();

        $articles = Article::orderBy('id')->get();
        $this->assertCount(1, $articles);

        $article = Article::find(1);
        $this->assertEquals(1, $article->id);

        $article->delete();

        $articles = Article::orderBy('id')->get();
        $this->assertCount(0, $articles);

        $article = Article::findOr(1, function () {
            return null;
        });
        $this->assertNull($article);

        $article = Article::withTrashed()->find('1');
        $this->assertEquals(1, $article->id);

        $article->restore();
        $article = Article::find(1);
        $this->assertEquals(1, $article->id);

        $articles = Article::orderBy('id')->get();
        $this->assertCount(1, $articles);
    }
}
