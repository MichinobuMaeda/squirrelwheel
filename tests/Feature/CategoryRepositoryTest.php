<?php

namespace Tests\Feature;

use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\CategoryRepository;
use App\Models\Category;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listCategories().
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

        $categories = (new CategoryRepository())->list();
        $this->assertCount(3, $categories);
        $this->assertEquals(1, $categories[0]->id);
        $this->assertEquals(2, $categories[1]->id);
        $this->assertEquals(4, $categories[2]->id);

        $categories = (new CategoryRepository())->list(true);
        $this->assertCount(4, $categories);
        $this->assertEquals(1, $categories[0]->id);
        $this->assertEquals(2, $categories[1]->id);
        $this->assertEquals(3, $categories[2]->id);
        $this->assertEquals(4, $categories[3]->id);
    }
}
