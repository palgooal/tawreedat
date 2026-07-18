<?php

namespace App\Notifications;

use App\Filament\Resources\CompanyRegistrationRequestResource;
use App\Models\CompanyRegistrationRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent synchronously (no ShouldQueue) from
 * CompanyRegistrationRequestController@store, wrapped in a try/catch there
 * - a mail failure must never prevent the request itself from being saved.
 * See docs/DEPLOYMENT_CHECKLIST.md for the MAIL_* settings this depends on
 * in production.
 */
class NewCompanyRegistrationRequestNotification extends Notification
{
    public function __construct(
        public readonly CompanyRegistrationRequest $companyRegistrationRequest,
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
        $request = $this->companyRegistrationRequest;

        return (new MailMessage)
            ->subject('طلب تسجيل شركة جديد في توريد')
            ->greeting('طلب تسجيل شركة جديد')
            ->line('وصل طلب تسجيل شركة جديد عبر نموذج "سجّل شركتك" في منصة توريد:')
            ->line('اسم الشركة: '.$request->company_name)
            ->line('اسم مسؤول التواصل: '.$request->contact_name)
            ->line('الجوال: '.$request->phone)
            ->line('البريد: '.($request->email ?: 'غير مُدخل'))
            ->line('المدينة: '.($request->city ?: 'غير مُدخلة'))
            ->line('التصنيف: '.($request->category ?: 'غير محدد'))
            ->line('الموقع الإلكتروني: '.($request->website ?: 'غير مُدخل'))
            ->line('الوصف: '.($request->description ?: '—'))
            ->action('فتح الطلب في لوحة التحكم', CompanyRegistrationRequestResource::getUrl('edit', ['record' => $request]))
            ->line('تذكير: لا توجد مدفوعات إلكترونية - أي اتفاق أو تحصيل يتم عبر التواصل المباشر (واتساب/هاتف/بريد).');
    }
}
