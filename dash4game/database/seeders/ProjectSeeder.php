<?php

namespace Database\Seeders;

use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use App\Enums\SprintStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Enums\WikiPageCategory;
use App\Models\ProgressReport;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $demo  = User::where('email', 'demo@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();
        $bob   = User::where('email', 'bob@example.com')->first();
        $carol = User::where('email', 'carol@example.com')->first();

        // ── Project 1 ─────────────────────────────────────────────────────────
        $shadow = Project::create([
            'owner_id'    => $demo->id,
            'name'        => 'Shadow Realm Online',
            'slug'        => 'shadow-realm-online',
            'description' => 'A dark fantasy MMORPG set in the cursed Shadow Realm. Players must unite to banish the eternal darkness.',
            'genre'       => 'MMORPG',
            'status'      => ProjectStatus::Production,
        ]);

        ProjectMember::insert([
            ['project_id' => $shadow->id, 'user_id' => $demo->id,  'role' => 'owner',  'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $shadow->id, 'user_id' => $alice->id, 'role' => 'admin',  'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $shadow->id, 'user_id' => $bob->id,   'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $shadow->id, 'user_id' => $carol->id, 'role' => 'viewer', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $completedSprint = Sprint::create([
            'project_id' => $shadow->id,
            'name'       => 'Raid 1 — World Foundation',
            'goal'       => 'Establish core gameplay loop and world map',
            'status'     => SprintStatus::Completed,
            'start_date' => now()->subMonths(2),
            'end_date'   => now()->subMonths(1)->subWeeks(2),
        ]);

        $activeSprint = Sprint::create([
            'project_id' => $shadow->id,
            'name'       => 'Raid 2 — Combat System',
            'goal'       => 'Build player combat mechanics and enemy AI',
            'status'     => SprintStatus::Active,
            'start_date' => now()->subWeeks(1),
            'end_date'   => now()->addWeeks(2),
        ]);

        Sprint::create([
            'project_id' => $shadow->id,
            'name'       => 'Raid 3 — Dungeon Design',
            'goal'       => 'Design and implement the first dungeon',
            'status'     => SprintStatus::Planned,
            'start_date' => now()->addWeeks(3),
            'end_date'   => now()->addWeeks(5),
        ]);

        $shadowTasks = [
            ['Create character selection screen', TaskType::Design,   TaskStatus::Done,       TaskPriority::High,     $activeSprint->id, $alice->id],
            ['Implement inventory UI',            TaskType::Feature,  TaskStatus::InProgress,  TaskPriority::High,     $activeSprint->id, $bob->id],
            ['Fix enemy hitbox issue',            TaskType::Bug,      TaskStatus::Todo,        TaskPriority::Critical, $activeSprint->id, $demo->id],
            ['Design first dungeon map',          TaskType::Design,   TaskStatus::InProgress,  TaskPriority::Medium,   $activeSprint->id, $alice->id],
            ['Add basic combat loop',             TaskType::Feature,  TaskStatus::Review,      TaskPriority::High,     $activeSprint->id, $bob->id],
            ['Shadow Realm ambient music',        TaskType::Sound,    TaskStatus::Todo,        TaskPriority::Medium,   $activeSprint->id, $carol->id],
            ['Write intro cutscene script',       TaskType::Story,    TaskStatus::Backlog,     TaskPriority::Low,      null,              $demo->id],
            ['Optimise pathfinding algorithm',    TaskType::Technical, TaskStatus::Backlog,    TaskPriority::Medium,   null,              $bob->id],
        ];

        foreach ($shadowTasks as $i => [$title, $type, $status, $priority, $sprint, $assignee]) {
            Task::create([
                'project_id'  => $shadow->id,
                'sprint_id'   => $sprint,
                'created_by'  => $demo->id,
                'assignee_id' => $assignee,
                'title'       => $title,
                'type'        => $type,
                'status'      => $status,
                'priority'    => $priority,
                'order_index' => $i,
            ]);
        }

        WikiPage::create([
            'project_id' => $shadow->id,
            'created_by' => $demo->id,
            'title'      => 'Project Overview',
            'slug'       => 'project-overview',
            'category'   => WikiPageCategory::GameDesign,
            'content'    => "# Shadow Realm Online\n\nA dark fantasy MMORPG where players must unite to banish the eternal darkness.\n\n## Core Pillars\n\n- Tight, skill-based combat\n- Rich lore and world-building\n- Cooperative raid mechanics",
        ]);

        WikiPage::create([
            'project_id' => $shadow->id,
            'created_by' => $alice->id,
            'title'      => 'Combat Design',
            'slug'       => 'combat-design',
            'category'   => WikiPageCategory::Mechanics,
            'content'    => "# Combat Design\n\nReal-time combat with cooldown-based abilities.\n\n## Core Mechanics\n\n- Light attack, heavy attack, dodge\n- Stamina system\n- Elemental weaknesses",
        ]);

        ProgressReport::create([
            'project_id'       => $shadow->id,
            'created_by'       => $demo->id,
            'title'            => 'Raid 1 Complete — World Foundation',
            'period_start_date' => now()->subMonths(2),
            'period_end_date'  => now()->subMonths(1)->subWeeks(2),
            'summary'          => 'Successfully completed the world foundation sprint. Core gameplay loop is in place.',
            'completed_work'   => "- Player movement system\n- Basic collision detection\n- World map prototype\n- Scene management",
            'current_blockers' => null,
            'next_goals'       => "Focus on combat system and enemy AI in Raid 2.",
        ]);

        // ── Project 2 ─────────────────────────────────────────────────────────
        $pixel = Project::create([
            'owner_id'    => $alice->id,
            'name'        => 'Pixel Dungeon Raiders',
            'slug'        => 'pixel-dungeon-raiders',
            'description' => 'A retro-style roguelike dungeon crawler with procedural generation and permadeath.',
            'genre'       => 'Roguelike',
            'status'      => ProjectStatus::PreProduction,
        ]);

        ProjectMember::insert([
            ['project_id' => $pixel->id, 'user_id' => $alice->id, 'role' => 'owner',  'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $pixel->id, 'user_id' => $demo->id,  'role' => 'admin',  'created_at' => now(), 'updated_at' => now()],
            ['project_id' => $pixel->id, 'user_id' => $bob->id,   'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pixelSprint = Sprint::create([
            'project_id' => $pixel->id,
            'name'       => 'Raid 1 — Prototype',
            'goal'       => 'Proof-of-concept dungeon generation',
            'status'     => SprintStatus::Active,
            'start_date' => now()->subWeeks(2),
            'end_date'   => now()->addWeeks(1),
        ]);

        $pixelTasks = [
            ['Procedural dungeon generator',  TaskType::Technical, TaskStatus::InProgress, TaskPriority::Critical, $pixelSprint->id, $bob->id],
            ['Pixel art character sprites',   TaskType::Art,       TaskStatus::InProgress, TaskPriority::High,     $pixelSprint->id, $alice->id],
            ['Basic movement and collision',  TaskType::Feature,   TaskStatus::Done,       TaskPriority::High,     $pixelSprint->id, $bob->id],
            ['Game design document',          TaskType::Design,    TaskStatus::Todo,       TaskPriority::Medium,   null,             $alice->id],
        ];

        foreach ($pixelTasks as $i => [$title, $type, $status, $priority, $sprint, $assignee]) {
            Task::create([
                'project_id'  => $pixel->id,
                'sprint_id'   => $sprint,
                'created_by'  => $alice->id,
                'assignee_id' => $assignee,
                'title'       => $title,
                'type'        => $type,
                'status'      => $status,
                'priority'    => $priority,
                'order_index' => $i,
            ]);
        }

        WikiPage::create([
            'project_id' => $pixel->id,
            'created_by' => $alice->id,
            'title'      => 'Game Concept',
            'slug'       => 'game-concept',
            'category'   => WikiPageCategory::GameDesign,
            'content'    => "# Pixel Dungeon Raiders\n\nA retro roguelike with procedural generation.\n\n## Key Features\n\n- Permadeath\n- Procedurally generated floors\n- Pixel art aesthetic",
        ]);
    }
}
