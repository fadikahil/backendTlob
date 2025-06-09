<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailService {
    public static function send(Mailable $email) : void {
        Mail::to('contact@tlobni.com')->send($email);
    }
}