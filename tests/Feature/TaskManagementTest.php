<?php

namespace Tests\Feature;

use App\Livewire\Task\TaskPage;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TaskSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $adminOne;
    private User $adminTwo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, TaskSeeder::class]);

        $this->superAdmin = User::where('username', 'super-admin')->first();
        $this->adminOne = User::where('username', 'admin-1')->first();
        $this->adminTwo = User::where('username', 'admin-2')->first();
    }

    public function test_dashboard_shows_greeting_and_stats(): void
    {
        $this->actingAs($this->adminOne)
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('Halo, ' . $this->adminOne->name);
    }

    public function test_task_pages_follow_role_access(): void
    {
        $this->actingAs($this->adminOne)->get('/admin/tasks')->assertStatus(200);
        $this->actingAs($this->adminOne)->get('/admin/all-tasks')->assertStatus(403);
        $this->actingAs($this->adminOne)->get('/admin/categories')->assertStatus(403);

        $this->actingAs($this->superAdmin)->get('/admin/tasks')->assertStatus(200);
        $this->actingAs($this->superAdmin)->get('/admin/all-tasks')->assertStatus(200);
        $this->actingAs($this->superAdmin)->get('/admin/categories')->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/tasks')->assertRedirect('/login');
    }

    public function test_user_can_create_own_task(): void
    {
        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->set('title', 'Tugas uji otomatis')
            ->set('priority', Task::PRIORITY_TINGGI)
            ->set('status', Task::STATUS_BELUM)
            ->set('due_date', now()->addDays(3)->toDateString())
            ->call('saveData');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->adminOne->id,
            'title' => 'Tugas uji otomatis',
            'priority' => Task::PRIORITY_TINGGI,
        ]);
    }

    public function test_validation_rejects_invalid_priority_and_missing_title(): void
    {
        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->set('title', 'Tugas uji')
            ->set('priority', 'darurat')
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData')
            ->assertHasErrors(['priority']);

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->set('title', '')
            ->set('priority', Task::PRIORITY_SEDANG)
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData')
            ->assertHasErrors(['title']);
    }

    public function test_user_can_update_own_task_status(): void
    {
        $task = Task::where('user_id', $this->adminOne->id)
            ->where('status', '!=', Task::STATUS_SELESAI)
            ->first();

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->call('listenEditData', ['id' => $task->id])
            ->set('status', Task::STATUS_SELESAI)
            ->call('saveData');

        $this->assertSame(Task::STATUS_SELESAI, $task->fresh()->status);
    }

    public function test_user_can_delete_own_task(): void
    {
        $task = Task::where('user_id', $this->adminOne->id)->first();

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->call('prepareDeleteData', ['id' => $task->id])
            ->call('dropData');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_edit_other_users_task(): void
    {
        $foreignTask = Task::where('user_id', $this->adminTwo->id)->first();

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->call('listenEditData', ['id' => $foreignTask->id])
            ->set('title', 'Percobaan serobot')
            ->set('priority', Task::PRIORITY_SEDANG)
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData');

        $this->assertNotSame('Percobaan serobot', $foreignTask->fresh()->title);
    }

    public function test_user_cannot_delete_other_users_task(): void
    {
        $foreignTask = Task::where('user_id', $this->adminTwo->id)->first();

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->set('id', $foreignTask->id)
            ->call('dropData');

        $this->assertDatabaseHas('tasks', ['id' => $foreignTask->id]);
    }

    public function test_bulk_delete_only_removes_own_tasks(): void
    {
        $ownTask = Task::where('user_id', $this->adminOne->id)->first();
        $foreignTask = Task::where('user_id', $this->adminTwo->id)->first();

        Livewire::actingAs($this->adminOne)
            ->test(TaskPage::class)
            ->call('dropBulkData', [(string) $ownTask->id, (string) $foreignTask->id]);

        $this->assertDatabaseMissing('tasks', ['id' => $ownTask->id]);
        $this->assertDatabaseHas('tasks', ['id' => $foreignTask->id]);
    }

    public function test_dashboard_stats_match_actual_counts(): void
    {
        $own = Task::where('user_id', $this->adminOne->id);
        $expectedTotal = (clone $own)->count();
        $expectedSelesai = (clone $own)->where('status', Task::STATUS_SELESAI)->count();

        $component = Livewire::actingAs($this->adminOne)->test(\App\Livewire\Admin\Dashboard\DashboardPage::class);

        $this->assertSame($expectedTotal, Task::where('user_id', $this->adminOne->id)->count());
        $this->assertSame($expectedSelesai, (clone $own)->where('status', Task::STATUS_SELESAI)->count());
    }
}
