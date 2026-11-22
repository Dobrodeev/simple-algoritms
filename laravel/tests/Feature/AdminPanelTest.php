<?php

namespace Tests\Feature;

use App\Filament\Resources\CourseResource\Pages\CreateCourse;
use App\Filament\Resources\CourseResource\Pages\EditCourse;
use App\Filament\Resources\CourseResource\Pages\ListCourses;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['admin.email' => 'admin@example.com', 'admin.password' => 'secret-test-pass']);
        $this->seed(AdminUserSeeder::class);
        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
    }

    public function test_seeder_creates_admin_with_hashed_password(): void
    {
        $this->assertTrue(Hash::check('secret-test-pass', $this->admin->password));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_only_configured_admin_can_access_panel(): void
    {
        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => 'x']);

        $this->actingAs($other)->get('/admin/courses')->assertForbidden();
        $this->actingAs($this->admin)->get('/admin/courses')->assertOk();
    }

    public function test_admin_can_list_create_edit_and_delete_courses(): void
    {
        $this->actingAs($this->admin);
        $eth = Course::create(['symbol' => 'ETHBTC', 'price' => '0.034']);

        Livewire::test(ListCourses::class)->assertCanSeeTableRecords([$eth]);

        Livewire::test(CreateCourse::class)
            ->fillForm(['symbol' => 'bnbusdt', 'price' => '612.5'])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertDatabaseHas('courses', ['symbol' => 'BNBUSDT']);

        Livewire::test(CreateCourse::class)
            ->fillForm(['symbol' => 'ethbtc', 'price' => '1'])
            ->call('create')
            ->assertHasFormErrors(['symbol']);
        $this->assertSame(2, Course::count());

        Livewire::test(EditCourse::class, ['record' => $eth->getKey()])
            ->fillForm(['price' => '0.05'])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertEquals('0.05', $eth->fresh()->price);

        Livewire::test(ListCourses::class)->callTableAction('delete', $eth);
        $this->assertModelMissing($eth);
    }
}
