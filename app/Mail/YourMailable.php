<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class YourMailable extends Mailable
{
    public $nama;
    public $email_kantor;
    public $password;

    public function __construct($nama, $email_kantor, $password)
    {
        $this->nama = $nama;
        $this->email_kantor = $email_kantor;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('email.register_informasi')
                    ->with([
                        'nama' => $this->nama,
                        'email_kantor' => $this->email_kantor,
                        'password' => $this->password,
                    ]);
    }
}