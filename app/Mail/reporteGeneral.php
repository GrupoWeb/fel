<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class reporteGeneral extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $path;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        // public_path('files\\'.$this->file)

        
        return $this->markdown('Mails.reporte')->attach(storage_path($this->path));
    }
}
