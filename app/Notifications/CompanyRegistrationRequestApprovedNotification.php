<?php

namespace App\Notifications;

use App\Filament\Resources\CompanyResource;
use App\Models\CompanyRegistrationRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent synchronously from CompanyRegistrationRequestResource::approve(),
 * wrapped in a try/catch there so a mail failure never blocks the approval
 * itself (the Company record and the request's status are already saved by
 * the time this runs). Informational only - it does NOT go to the company
 * that registered; see docs/DECISIONS.md for why company-facing email is
 * intentionally out of scope for now.
 */
class CompanyRegistrationRequestApprovedNotification extends Notification
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
            ->subject('تمت الموافقة على طلب تسجيل شركة في توريدات')
            ->greeting('تمت الموافقة على طلب')
            ->line('تمت الموافقة على طلب تسجيل الشركة التالية، وتم إنشاء/تحديث سجل الشركة تلقائياً:')
            ->line('اسم الشركة: '.$request->company_name)
            ->action('فتح سجل الشركة', CompanyResource::getUrl('edit', ['record' => $request->company_id]))
            ->line('تذكير: لا توجد مدفوعات إلكترونية - أي اتفاق أو تحصيل يتم عبر التواصل المباشر (واتساب/هاتف/بريد).');
    }
}
