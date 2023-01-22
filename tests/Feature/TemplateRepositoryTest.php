<?php

namespace Tests\Feature;

use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\TemplateRepository;
use App\Models\Category;
use App\Models\Template;

class TemplateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listTemplates().
     *
     * @return void
     */
    public function test_list()
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

        $templates = (new TemplateRepository())->list();
        $this->assertCount(5, $templates);
        $this->assertEquals(1, $templates[0]->id);
        $this->assertEquals(2, $templates[1]->id);
        $this->assertEquals(3, $templates[2]->id);
        $this->assertEquals(5, $templates[3]->id);
        $this->assertEquals(6, $templates[4]->id);

        $templates = (new TemplateRepository())->list(true);
        $this->assertCount(6, $templates);
        $this->assertEquals(1, $templates[0]->id);
        $this->assertEquals(2, $templates[1]->id);
        $this->assertEquals(3, $templates[2]->id);
        $this->assertEquals(4, $templates[3]->id);
        $this->assertEquals(5, $templates[4]->id);
        $this->assertEquals(6, $templates[5]->id);
    }
}
