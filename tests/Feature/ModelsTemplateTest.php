<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use DateTime;
use App\Models\Category;
use App\Models\Template;

class ModelsTemplateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test create().
     *
     * @return void
     */
    public function test_create()
    {
        $ts = new DateTime('2020-01-01T12:34:56.000+0900');

        $templates = Template::orderBy('id')->get();
        $this->assertCount(0, $templates);

        Category::create([
            'id' => '1',
            'name' => 'Name 1',
            'priority' => 1,
        ])->save();

        Template::create([
            'category_id' => '1',
            'name' => 'Name 1',
            'body' => 'Body 1',
            'used_at' => new DateTime,
        ])->save();

        Template::create([
            'category_id' => '1',
            'name' => 'Name 2',
            'body' => 'Body 2',
            'used_at' => $ts,
        ])->save();

        $templates = Template::orderBy('id')->get();
        $this->assertCount(2, $templates);

        $template = $templates[0];
        $this->assertEquals(1, $template->id);
        $this->assertEquals('1', $template->category_id);
        $this->assertEquals('Name 1', $template->name);
        $this->assertEquals('Body 1', $template->body);
        $this->assertIsObject($template->used_at);

        $template = $templates[1];
        $this->assertEquals(2, $template->id);
        $this->assertEquals('1', $template->category_id);
        $this->assertEquals('Name 2', $template->name);
        $this->assertEquals('Body 2', $template->body);
        $this->assertEquals($ts, $template->used_at);
    }

    /**
     * Test update().
     *
     * @return void
     */
    public function test_update()
    {
        $ts = new DateTime('2020-01-01T12:34:56.000+0900');

        Category::create([
            'id' => '1',
            'name' => 'Name 1',
            'priority' => 1,
        ])->save();

        Category::create([
            'id' => '2',
            'name' => 'Name 2',
            'priority' => 2,
        ])->save();

        Template::create([
            'category_id' => '1',
            'name' => 'Name 1',
            'body' => 'Body 1',
            'used_at' => new DateTime,
        ])->save();

        $template = Template::find(1);
        $template->fill([
            'category_id' => '2',
            'name' => 'Name 2',
            'body' => 'Body 2',
            'used_at' => $ts,
        ]);
        $template->save();

        $template = Template::find(1);
        $this->assertEquals(1, $template->id);
        $this->assertEquals('2', $template->category_id);
        $this->assertEquals('Name 2', $template->name);
        $this->assertEquals('Body 2', $template->body);
        $this->assertEquals($ts, $template->used_at);
    }

    /**
     * Test delete().
     *
     * @return void
     */
    public function test_delete()
    {
        $templates = Template::orderBy('id')->get();
        $this->assertCount(0, $templates);

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
            'used_at' => new DateTime,
        ])->save();

        $templates = Template::orderBy('id')->get();
        $this->assertCount(1, $templates);

        $template = Template::find(1);
        $this->assertEquals(1, $template->id);

        $template->delete();

        $templates = Template::orderBy('id')->get();
        $this->assertCount(0, $templates);

        $template = Template::findOr(1, function () {
            return null;
        });
        $this->assertNull($template);

        $template = Template::withTrashed()->find(1);
        $this->assertEquals('1', $template->id);

        $template->restore();
        $template = Template::find('1');
        $this->assertEquals('1', $template->id);
    }
}
