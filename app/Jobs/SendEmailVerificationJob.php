<?php

namespace App\Jobs;

use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailVerificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $user,
        public readonly string $code,
        public readonly string $email,
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new EmailVerificationMail($this->user, $this->code));
    }
}
