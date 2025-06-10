<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailService {
    public static function send(Mailable $email) : void {
        Mail::to('fadialkahil@gmail.com')->send($email);
    }
}