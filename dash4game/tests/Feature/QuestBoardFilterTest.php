<?php

namespace Tests\Feature;

use App\Enums\ProjectRole;
use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestBoardFilterTest extends TestCase
{
    use RefreshDatabase;

    private function projectWithOwner(): array
    {
        $owner   = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        return [$owner, $project];
    }

    // ── No active sprint ───────────────────────────────────────────────────

    public function test_board_shows_empty_state_when_no_active_sprint(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Planned]);

        $this->actingAs($owner)
            ->get(route('projects.quests.index', $project))
            ->assertOk()
            ->assertSee('No Active Raid Sprint');
    }

    // ── Active sprint filtering ────────────────────────────────────────────

    public function test_task_in_active_sprint_appears_on_board(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        $sprint = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        $task   = Task::factory()->create(['project_id' => $project->id, 'sprint_id' => $sprint->id, 'created_by' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('projects.quests.index', $project))
            ->assertOk()
            ->assertSee($task->title);
    }

    public function test_backlog_task_does_not_appear_on_board(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        $backlog = Task::factory()->create(['project_id' => $project->id, 'sprint_id' => null, 'created_by' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('projects.quests.index', $project))
            ->assertOk()
            ->assertDontSee($backlog->title);
    }

    public function test_task_in_planned_sprint_does_not_appear_on_board(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        $planned = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Planned]);
        $task    = Task::factory()->create(['project_id' => $project->id, 'sprint_id' => $planned->id, 'created_by' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('projects.quests.index', $project))
            ->assertOk()
            ->assertDontSee($task->title);
    }

    public function test_task_in_completed_sprint_does_not_appear_on_board(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        $completed = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Completed]);
        $task      = Task::factory()->create(['project_id' => $project->id, 'sprint_id' => $completed->id, 'created_by' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('projects.quests.index', $project))
            ->assertOk()
            ->assertDontSee($task->title);
    }

    // ── Authorization ──────────────────────────────────────────────────────

    public function test_unauthenticated_user_is_redirected(): void
    {
        $project = Project::factory()->create();

        $this->get(route('projects.quests.index', $project))
            ->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_view_board(): void
    {
        $project   = Project::factory()->create();
        $outsider  = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('projects.quests.index', $project))
            ->assertForbidden();
    }

    public function test_viewer_member_can_view_board(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        $viewer = User::factory()->create();
        ProjectMember::create(['project_id' => $project->id, 'user_id' => $viewer->id, 'role' => ProjectRole::Viewer]);

        $this->actingAs($viewer)
            ->get(route('projects.quests.index', $project))
            ->assertOk();
    }
}
