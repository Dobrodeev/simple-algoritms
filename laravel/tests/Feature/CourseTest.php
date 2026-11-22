<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Services\CourseJsonStore;
use Database\Seeders\CourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private string $jsonPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonPath = storage_path('framework/testing/courses.json');
        File::delete($this->jsonPath);
        $this->app->instance(CourseJsonStore::class, new CourseJsonStore($this->jsonPath, database_path('data/courses.json')));
    }

    protected function tearDown(): void
    {
        File::delete($this->jsonPath);
        parent::tearDown();
    }

    public function test_seeder_loads_every_pair_from_json(): void
    {
        $this->seed(CourseSeeder::class);

        $source = json_decode(File::get(database_path('data/courses.json')), true);
        $this->assertSame(count($source), Course::count());
        $this->assertEquals('10141.48', Course::where('symbol', 'BTCUSDT')->value('price'));
    }

    public function test_index_shows_highlighted_courses_from_db(): void
    {
        Course::create(['symbol' => 'ETHBTC', 'price' => '0.034066']);
        Course::create(['symbol' => 'BTCUSDT', 'price' => '10141.48']);

        $this->get('/')
            ->assertOk()
            ->assertSee('class="pair-btc"', false)
            ->assertSee('class="pair-usdt"', false)
            ->assertSee('ETHBTC');
    }

    public function test_store_adds_course_to_db(): void
    {
        $this->post('/courses', ['symbol' => ' bnbusdt ', 'price' => '612.5'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('courses', ['symbol' => 'BNBUSDT']);
    }

    public function test_store_rejects_duplicates_and_bad_input(): void
    {
        Course::create(['symbol' => 'ETHBTC', 'price' => '1']);

        $this->post('/courses', ['symbol' => 'ETHBTC', 'price' => '2'])->assertSessionHasErrors('symbol');
        $this->post('/courses', ['symbol' => 'BAD-PAIR', 'price' => 'abc'])->assertSessionHasErrors(['symbol', 'price']);
        $this->assertSame(1, Course::count());
    }

    public function test_json_page_and_store(): void
    {
        $this->get('/json')->assertOk()->assertSee('ETHBTC');

        $this->post('/json', ['symbol' => 'NEWETH', 'price' => '0.5'])->assertRedirect('/json');

        $data = json_decode(File::get($this->jsonPath), true);
        $this->assertSame('0.5', $data['NEWETH']);
        $this->assertArrayHasKey('ETHBTC', $data);
    }
}
