<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use DateTime;
use App\Models\Category;
use App\Models\Template;

class ModelsCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test create().
     *
     * @return void
     */
    public function test_create()
    {
        $categories = Category::orderBy('id')->get();
        $this->assertCount(0, $categories);

        Category::create([
            'id' => '1',
            'name' => 'Name 1',
            'update_only' => true,
            'priority' => 1,
        ])->save();

        Category::create([
            'id' => '2',
            'name' => 'Name 2',
            'priority' => 2,
        ])->save();

        $categories = Category::orderBy('id')->get();
        $this->assertCount(2, $categories);

        $category = $categories[0];
        $this->assertEquals('1', $category->id);
        $this->assertEquals('Name 1', $category->name);
        $this->assertTrue($category->update_only);
        $this->assertEquals(1, $category->priority);
        $this->assertNull($category->checked_at);
        $this->assertCount(0, $category->templates);

        $category = $categories[1];
        $this->assertEquals('2', $category->id);
        $this->assertEquals('Name 2', $category->name);
        $this->assertFalse($category->update_only);
        $this->assertEquals(2, $category->priority);
        $this->assertNull($category->checked_at);
        $this->assertCount(0, $category->templates);
    }

    /**
     * Test update().
     *
     * @return void
     */
    public function test_update()
    {
        $ts = new DateTime('2020-01-01T12:34:56.000Z');

        Category::create([
            'id' => '1',
            'name' => 'Name 1',
            'update_only' => true,
            'priority' => 1,
        ])->save();

        Template::create([
            'category_id' => '1',
            'name' => 'Name 1',
            'body' => 'Body 1',
        ])->save();

        $category = Category::find('1');
        $category->fill([
            'id' => '2',
            'name' => 'Name 2',
            'update_only' => false,
            'priority' => 2,
            'checked_at' => $ts,
        ]);

        $category->save();
        $category = Category::find('2');
        $this->assertEquals('2', $category->id);
        $this->assertEquals('Name 2', $category->name);
        $this->assertFalse($category->update_only);
        $this->assertEquals(2, $category->priority);
        $this->assertEquals($ts, $category->checked_at);
        $this->assertCount(1, $category->templates);

        $category->fill([
            'id' => '1',
            'name' => 'Name 1',
            'update_only' => true,
            'priority' => 1,
            'checked_at' => null,
        ]);

        $category->save();
        $category = Category::find('1');
        $this->assertEquals('1', $category->id);
        $this->assertEquals('Name 1', $category->name);
        $this->assertTrue($category->update_only);
        $this->assertEquals(1, $category->priority);
        $this->assertNull($category->checked_at);
        $this->assertCount(1, $category->templates);
    }

    /**
     * Test delete().
     *
     * @return void
     */
    public function test_delete()
    {
        $categories = Category::orderBy('id')->get();
        $this->assertCount(0, $categories);

        Category::create([
            'id' => '1',
            'name' => 'Name 1',
            'update_only' => true,
            'priority' => 1,
        ])->save();

        $categories = Category::orderBy('id')->get();
        $this->assertCount(1, $categories);

        Template::create([
            'category_id' => '1',
            'name' => 'Name 1',
            'body' => 'Body 1',
        ])->save();

        $category = Category::find('1');
        $this->assertEquals('1', $category->id);

        $category->delete();

        $categories = Category::orderBy('id')->get();
        $this->assertCount(0, $categories);

        $category = Category::findOr('1', function () {
            return null;
        });
        $this->assertNull($category);

        $category = Category::withTrashed()->find('1');
        $this->assertEquals('1', $category->id);

        $category->restore();
        $category = Category::find('1');
        $this->assertEquals('1', $category->id);

        $categories = Category::orderBy('id')->get();
        $this->assertCount(1, $categories);
   }
}
