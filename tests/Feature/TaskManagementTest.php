<?php

namespace Tests\Feature;

use App\Livewire\Admin\AllTask\AllTaskPage;
use App\Livewire\Admin\User\UserPage;
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

    private User $admin;
    private User $userOne;
    private User $userTwo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, TaskSeeder::class]);

        $this->admin = User::where('username', 'admin')->first();
        $this->userOne = User::where('username', 'user-1')->first();
        $this->userTwo = User::where('username', 'user-2')->first();
    }

    public function test_dashboard_shows_greeting_and_stats(): void
    {
        $this->actingAs($this->userOne)
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('Halo, ' . $this->userOne->name);
    }

    public function test_task_pages_follow_role_access(): void
    {
        $this->actingAs($this->userOne)->get('/admin/tasks')->assertStatus(200);
        $this->actingAs($this->userOne)->get('/admin/all-tasks')->assertStatus(403);
        $this->actingAs($this->userOne)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($this->userOne)->get('/admin/users')->assertStatus(403);
        $this->actingAs($this->userOne)->get('/admin/settings')->assertStatus(403);

        $this->actingAs($this->admin)->get('/admin/tasks')->assertRedirect(route('admin.all-tasks'));
        $this->actingAs($this->admin)->get('/admin/all-tasks')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/categories')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/users')->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/tasks')->assertRedirect('/login');
    }

    public function test_seeded_roles_match_new_semantics(): void
    {
        $this->assertSame('admin', $this->admin->role);
        $this->assertSame('user', $this->userOne->role);
        $this->assertSame('user', $this->userTwo->role);
        $this->assertSame(0, Task::where('user_id', $this->admin->id)->count());
    }

    public function test_user_can_create_own_task(): void
    {
        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->set('title', 'Tugas uji otomatis')
            ->set('priority', Task::PRIORITY_TINGGI)
            ->set('status', Task::STATUS_BELUM)
            ->set('due_date', now()->addDays(3)->toDateString())
            ->call('saveData');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->userOne->id,
            'title' => 'Tugas uji otomatis',
            'priority' => Task::PRIORITY_TINGGI,
        ]);
    }

    public function test_validation_rejects_invalid_priority_and_missing_title(): void
    {
        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->set('title', 'Tugas uji')
            ->set('priority', 'darurat')
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData')
            ->assertHasErrors(['priority']);

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->set('title', '')
            ->set('priority', Task::PRIORITY_SEDANG)
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData')
            ->assertHasErrors(['title']);
    }

    public function test_user_can_update_own_task_status(): void
    {
        $task = Task::where('user_id', $this->userOne->id)
            ->where('status', '!=', Task::STATUS_SELESAI)
            ->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->call('listenEditData', ['id' => $task->id])
            ->set('status', Task::STATUS_SELESAI)
            ->call('saveData');

        $this->assertSame(Task::STATUS_SELESAI, $task->fresh()->status);
    }

    public function test_user_can_delete_own_task(): void
    {
        $task = Task::where('user_id', $this->userOne->id)->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->call('prepareDeleteData', ['id' => $task->id])
            ->call('dropData');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_edit_other_users_task(): void
    {
        $foreignTask = Task::where('user_id', $this->userTwo->id)->first();

        Livewire::actingAs($this->userOne)
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
        $foreignTask = Task::where('user_id', $this->userTwo->id)->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->set('id', $foreignTask->id)
            ->call('dropData');

        $this->assertDatabaseHas('tasks', ['id' => $foreignTask->id]);
    }

    public function test_bulk_delete_only_removes_own_tasks(): void
    {
        $ownTask = Task::where('user_id', $this->userOne->id)->first();
        $foreignTask = Task::where('user_id', $this->userTwo->id)->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->call('dropBulkData', [(string) $ownTask->id, (string) $foreignTask->id]);

        $this->assertDatabaseMissing('tasks', ['id' => $ownTask->id]);
        $this->assertDatabaseHas('tasks', ['id' => $foreignTask->id]);
    }

    public function test_admin_can_create_task_for_user(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AllTaskPage::class)
            ->set('user_id', $this->userOne->id)
            ->set('title', 'Tugas dari admin')
            ->set('priority', Task::PRIORITY_TINGGI)
            ->set('status', Task::STATUS_BELUM)
            ->set('due_date', now()->addDays(5)->toDateString())
            ->call('saveData');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->userOne->id,
            'title' => 'Tugas dari admin',
        ]);
    }

    public function test_admin_cannot_assign_task_to_another_admin(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AllTaskPage::class)
            ->set('user_id', $this->admin->id)
            ->set('title', 'Tugas untuk admin')
            ->set('priority', Task::PRIORITY_SEDANG)
            ->set('status', Task::STATUS_BELUM)
            ->call('saveData');

        $this->assertDatabaseMissing('tasks', [
            'user_id' => $this->admin->id,
            'title' => 'Tugas untuk admin',
        ]);
    }

    public function test_admin_can_edit_and_delete_any_task(): void
    {
        $userTask = Task::where('user_id', $this->userOne->id)->first();

        Livewire::actingAs($this->admin)
            ->test(AllTaskPage::class)
            ->call('listenEditData', ['id' => $userTask->id])
            ->set('status', Task::STATUS_SELESAI)
            ->call('saveData');

        $this->assertSame(Task::STATUS_SELESAI, $userTask->fresh()->status);

        Livewire::actingAs($this->admin)
            ->test(AllTaskPage::class)
            ->set('id', $userTask->id)
            ->call('dropData');

        $this->assertDatabaseMissing('tasks', ['id' => $userTask->id]);
    }

    public function test_last_admin_cannot_be_deleted(): void
    {
        $this->assertSame(1, User::where('role', 'admin')->count());

        Livewire::actingAs($this->admin)
            ->test(UserPage::class)
            ->set('id', $this->admin->id)
            ->call('dropData');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_user_can_open_own_task_detail_modal(): void
    {
        $task = Task::where('user_id', $this->userOne->id)->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->call('loadDetail', ['id' => $task->id])
            ->assertSet('showDetailModal', true)
            ->assertSet('detailTask.id', $task->id);
    }

    public function test_user_cannot_open_other_users_task_detail(): void
    {
        $foreignTask = Task::where('user_id', $this->userTwo->id)->first();

        Livewire::actingAs($this->userOne)
            ->test(TaskPage::class)
            ->call('loadDetail', ['id' => $foreignTask->id])
            ->assertSet('showDetailModal', false);
    }

    public function test_admin_can_open_any_task_detail(): void
    {
        $task = Task::where('user_id', $this->userTwo->id)->first();

        Livewire::actingAs($this->admin)
            ->test(AllTaskPage::class)
            ->call('loadDetail', ['id' => $task->id])
            ->assertSet('showDetailModal', true)
            ->assertSet('detailTask.id', $task->id);
    }
}
