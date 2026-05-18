<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail(object $notifiable): object
    {
        $url = URL::to('/auth/reset-password?token=' . $this->token);

        return $this->buildMailMessage($url);
    }
}
