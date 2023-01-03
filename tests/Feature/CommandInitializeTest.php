<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Console\Commands\Initialize;
use App\Models\Category;
use App\Models\Template;

class CommandInitializeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test handle().
     *
     * @return void
     */
    public function test_example()
    {
        (new Initialize())->handle();

        $categories = Category::orderBy('id')->get();
        $this->assertCount(2, $categories);

        $templates = Template::orderBy('id')->get();
        $this->assertCount(2, $templates);

        $category = $categories[0];
        $this->assertEquals(1, $category->id);
        $this->assertEquals('Immediate', $category->name);
        $this->assertNull($category->feed);
        $this->assertFalse($category->update_only);
        $this->assertEquals(0, $category->priority);
        $this->assertIsObject($category->checked_at);

        $this->assertCount(1, $category->templates);

        $template = $category->templates[0];
        $this->assertEquals('Immediate', $template->name);
        $this->assertEquals(<<<END
%%content%%
%%link%%
END, $template->body);
        $this->assertIsObject($template->used_at);

        $category = $categories[1];
        $this->assertEquals(2, $category->id);
        $this->assertEquals('Scheduled', $category->name);
        $this->assertNull($category->feed);
        $this->assertFalse($category->update_only);
        $this->assertEquals(1, $category->priority);
        $this->assertIsObject($category->checked_at);

        $this->assertCount(1, $category->templates);

        $template = $category->templates[0];
        $this->assertEquals('Scheduled', $template->name);
        $this->assertEquals(<<<END
%%content%%
%%link%%
END, $template->body);
        $this->assertIsObject($template->used_at);

        (new Initialize())->handle();

        $categories = Category::orderBy('id')->get();
        $this->assertCount(2, $categories);

        $templates = Template::orderBy('id')->get();
        $this->assertCount(2, $templates);
    }
}
