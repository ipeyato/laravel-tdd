<?php

namespace Tests\Unit\Models;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function task_model_has_toggle_status_method()
    {
        // Generate 1 record task pada table `tasks`.
        $task = Factory(Task::class)->create();

        // Panggil method `toggleStatus()` pada model `App\Task`
        $task->toggleStatus();
        
        // Kolom is_done pada record task berubah menjadi 1
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_done' => 1,
            ]);
            
        // Panggil method `toggleStatus()` pada model `App\Task` (lagi)
        $task->toggleStatus();

        // Kolom is_done pada record task berubah menjadi 0
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_done' => 0,
            ]);
    }
}
