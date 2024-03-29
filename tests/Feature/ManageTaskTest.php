<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Task;

class ManageTaskTest extends TestCase
{
    use RefreshDatabase; // Tambahkan baris ini agar setiap PHPUnit jalan, dia melakukan database migration.

    /** @test */
    public function user_can_create_task()
    {
        // User buka halaman Daftar Task
        $this->visit('/tasks'); //untuk mengunjungi halaman dengan URL tertentu.

        // Isi form `name` dan `description` kemudian submit
        $this->submitForm('Create Task', [
            'name' => 'My first Task',
            'description' => 'This is my first task on my new job'
        ]); //untuk mengisi form dan klik tombol “Create Task”.

        // Lihat Record tersimpan ke database
        $this->seeInDatabase('tasks', [
            'name' => 'My first Task',
            'description' => 'This is my first task on my new job',
            'is_done' => 0
        ]); // melihat isi tabel tasks pada database.    

        // Redirect ke halaman Daftar Task
        $this->seePageIs('tasks'); // melihat URL yang kita dapatkan setelah action sebelumnya.

        // Tampil hasil task yang telah diinput
        $this->see('My first Task'); //melihat isi tulisan yang tampil di halaman web.
        $this->see('This is my first task on my new job');
    }

    /** @test */
    public function task_entry_must_pass_validation()
    {
        // submit form untuk membuat task baru
        // dengan field name description kosong
        $this->post('/tasks', [
            'name' => '',
            'description' => '',
        ]);

        // Cek pada session apakah ada error untuk field nama dan description
        $this->assertSessionHasErrors(['name', 'description']);
    }

    /** @test */
    public function user_can_browser_task_index_page()
    {
        // Generate 3 record task pada table `tasks`.
        $tasks = Factory(Task::class, 3)->create();
        
        // User membuka halaman Daftar Task.
        $this->visit('/tasks');

        // User melihat ketiga task tampil pada halaman.
        $this->see($tasks[0]->name);
        $this->see($tasks[1]->name);
        $this->see($tasks[2]->name);

        // User melihat link untuk edit task pada masing-masing item task.

        // <a href="/tasks?action=edit&id=1" id="edit_task_1">edit</a>
        $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[0]->id,
            'href' => url('tasks?action=edit&id='.$tasks[0]->id)
        ]);

         // <a href="/tasks?action=edit&id=2" id="edit_task_2">edit</a>
         $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[1]->id,
            'href' => url('tasks?action=edit&id='.$tasks[1]->id)
        ]);

        // <a href="/tasks?action=edit&id=3" id="edit_task_3">edit</a>
        $this->seeElement('a', [
            'id'   => 'edit_task_'.$tasks[2]->id,
            'href' => url('tasks?action=edit&id='.$tasks[2]->id)
        ]);
    }

    /** @test */
    public function user_can_edit_an_existing_task()
    {
       // Generate 1 record task pada table `tasks`.
        $task = Factory(Task::class)->create();
        
        // User membuka halaman Daftar Task.
        $this->visit('/tasks');
        
        // Klik tombol edit task (link dengan id="edit_task_1")
        // Dimana angka 1 adalah id dari $task
        $this->click('edit_task_'.$task->id);
        
        // Lihat URL yang dituju sesuai dengan target
        $this->seePageIs('/tasks?action=edit&id='.$task->id);
        
        // Tampil form Edit Task, kita cek apakah ada form dengan
        // id="edit_task_1" dan action="tasks/1"
        $this->seeElement('form', [
            'id' => 'edit_task_'.$task->id,
            'action' => url('tasks/'.$task->id),
        ]);

        // User submit form berisi nama dan deskripsi task yang baru
        $this->submitForm('Update Task', [
            'name' => 'Update Task',
            'description' => 'Update task description'
        ]);

        // Lihat halaman web ter-redirect ke URL sesuai dengan target
        // yaitu '/tasks', kembali ke daftar task
        $this->see('/tasks'); 
        
        // Record pada database berubah sesuai dengan nama dan deskripsi baru
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'name' => 'Update Task',
            'description' => 'Update task description'
        ]);
    }

    /** @test */
    public function user_can_delete_an_existing_task()
    {
        // Generate 1 record task pada table `tasks`.
        $task = Factory(Task::class)->create();

        // User membuka halaman Daftar Task.
        $this->visit('/tasks');

        // User tekan tombol "Hapus Task" (tombol dengan id="edit_task_1")
        // Dimana angka 1 adalah id dari $task
        $this->press('delete_task_'.$task->id);

        // Lihat halaman web ter-redirect ke halaman daftar task
        $this->seePageIs('/tasks');

        // Record task hilang dari database
        $this->dontSeeInDatabase('tasks',[
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function user_can_toggle_task_status()
    {
        // Generate 1 record task pada table `tasks`.
        $task = Factory(Task::class)->create();

        // User membuka halaman Daftar Task.
        $this->visit('/tasks');

        // User tekan tombol dengan id="toggle_task_1"
        // Dimana angka 1 adalah id dari $task
        $this->press('toggle_task_'.$task->id);
        
        // Lihat halaman web ter-redirect ke halaman daftar task
        $this->seePageIs('/tasks');
        
        // Kolom is_done pada record task berubah menjadi 1
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_done' => 1
        ]);
            
        // User tekan tombol dengan id="toggle_task_1" (lagi)
        // untuk mengembalikan status task
        $this->press('toggle_task_'.$task->id);

        // Lihat halaman web ter-redirect ke halaman daftar task
        $this->visit('/tasks');

        // Kolom is_done pada record task berubah menjadi 0
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_done' => 0
        ]);
    }
}
