<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TareaEstadoCambiado extends Mailable
{
    use Queueable, SerializesModels;

    // A variable will be set to be accessed from anywhere where you want to use the case
    public $task;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $this->subject Define el asunto del correo electrónico
        // $this->markdown Use an email template in Markdown format
        // $this->with Sends the $task variable to the Markdown view, allowing access to the task data in the template
        return $this->subject('Estado de tarea actualizada')
                    ->markdown('emails.tarea-estado-cambiado')
                    ->with('task', $this->task);
    }
}
