<?php

namespace App\Notifications;

use App\Filament\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent synchronously (no ShouldQueue) from ContactController@store, wrapped
 * in a try/catch there - a mail failure must never prevent the inquiry
 * itself from being saved. See docs/DEPLOYMENT_CHECKLIST.md for the
 * MAIL_* settings this depends on in production.
 */
class NewContactRequestNotification extends Notification
{
    public function __construct(
        public readonly ContactRequest $contactRequest,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->contactRequest;

        return (new MailMessage)
            ->subject('طلب تواصل جديد من منصة توريدات')
            ->greeting('طلب تواصل جديد')
            ->line('وصل طلب تواصل جديد عبر نموذج التواصل في منصة توريدات:')
            ->line('الاسم: '.$request->name)
            ->line('البريد: '.$request->email)
            ->line('الهاتف: '.($request->phone ?: 'غير مُدخل'))
            ->line('الشركة: '.($request->company ?: 'غير مُدخلة'))
            ->line('نوع الطلب: '.($request->inquiry_type ?: 'غير محدد'))
            ->line('الرسالة:')
            ->line($request->message ?: '—')
            ->action('فتح الطلب في لوحة التحكم', ContactRequestResource::getUrl('edit', ['record' => $request]))
            ->line('يمكنك أيضاً مراجعة جميع طلبات التواصل من لوحة التحكم مباشرة.');
    }
}
