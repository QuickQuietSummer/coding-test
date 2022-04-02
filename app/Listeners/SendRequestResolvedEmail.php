<?php

namespace App\Listeners;

use App\Events\RequestResolved;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendRequestResolvedEmail
{
    /**
     * Handle the event.
     *
     * @param \App\Events\RequestResolved $event
     * @return void
     */
    public function handle(RequestResolved $event)
    {
        $user = User::whereId($event->request->user_id)->first();
        Mail::to($user->email)->queue(new \App\Mail\RequestResolved($event->request));
    }
}
