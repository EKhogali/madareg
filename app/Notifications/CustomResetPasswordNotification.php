<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends BaseNotification
{
    public string $url;

    protected function resetUrl($notifiable): string
    {
        return $this->url;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('رابط تغيير كلمة مرور حسابك في منظومة مدارج النور')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('لقد تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بحسابك.')
            ->action('إعادة تعيين كلمة المرور', $this->resetUrl($notifiable))
            ->line('صلاحية هذا الرابط 60 دقيقة من وقت إرسال هذه الرسالة.')
            ->line('إذا لم تطلب إعادة تعيين كلمة المرور، فلا داعي لاتخاذ أي إجراء.')
            ->salutation('مع تحيات، منتدى مدارج النور');
    }
}
