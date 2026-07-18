<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\CompanyRegistrationRequest;
use App\Models\User;
use App\Notifications\NewCompanyRegistrationRequestNotification;
use App\Support\Permissions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * "سجّل شركتك" is a request/review workflow, not a self-serve signup or
 * paid plan purchase - see docs/DECISIONS.md. This controller only ever
 * creates a CompanyRegistrationRequest with status=pending; approving it,
 * publishing a public profile, and any payment/collection all happen
 * outside this controller (admin panel + manual WhatsApp/phone/email).
 *
 * Mirrors ContactController's honeypot/throttle/notify-admins pattern
 * deliberately, for consistency - see that class for the same shape.
 */
class CompanyRegistrationRequestController extends Controller
{
    private const SUCCESS_MESSAGE = 'تم استلام طلب تسجيل شركتك بنجاح، وسيتواصل معك فريق توريد قريباً.';

    public function create(): View
    {
        return view('company-registration.create', [
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot: resources/views/company-registration/create.blade.php
        // renders a "hp_check" field hidden from real visitors the same
        // way the public contact form does (see ContactController for the
        // full history of why it's named this and not something more
        // guessable/autofill-prone). A real submission never has this
        // field filled in - if it is, pretend success, save nothing,
        // notify nobody, and skip validation so no error can leak back.
        if (filled($request->input('hp_check'))) {
            return back()->with('success', self::SUCCESS_MESSAGE);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Text fallback columns kept for backward compatibility (see
        // docs/DECISIONS.md) - snapshot the chosen city/category names so
        // the request stays human-readable even if the City/Category row
        // is later renamed or deleted.
        $city = $validated['city_id'] ?? null ? City::query()->find($validated['city_id']) : null;
        $category = $validated['category_id'] ?? null ? Category::query()->find($validated['category_id']) : null;

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('company-registration-logos', 'public')
            : null;

        $companyRegistrationRequest = CompanyRegistrationRequest::create([
            'company_name' => $validated['company_name'],
            'contact_name' => $validated['contact_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $city?->name,
            'category' => $category?->name,
            'city_id' => $validated['city_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo' => $logoPath,
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => CompanyRegistrationRequest::STATUS_PENDING,
        ]);

        $this->notifyAdmins($companyRegistrationRequest);

        return back()->with('success', self::SUCCESS_MESSAGE);
    }

    /**
     * Same failure-isolation contract as ContactController::notifyAdmins():
     * the request is already safely saved by the time this runs, so a mail
     * failure (bad MAIL_* config, SMTP timeout) is logged and swallowed,
     * never surfaced to the visitor or re-thrown.
     */
    private function notifyAdmins(CompanyRegistrationRequest $companyRegistrationRequest): void
    {
        try {
            $recipients = $this->resolveAdminRecipients();

            if ($recipients->isEmpty()) {
                return;
            }

            Notification::send($recipients, new NewCompanyRegistrationRequestNotification($companyRegistrationRequest));
        } catch (Throwable $e) {
            Log::error('Failed to send new company registration request notification.', [
                'company_registration_request_id' => $companyRegistrationRequest->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Every user who can currently see registration requests in the admin
     * panel - permission-driven (`view registration requests` / `manage
     * registration requests`, see docs/ADMIN_PANEL.md), which today means
     * Super Admin, Admin, and Support - plus any legacy is_admin=true
     * account, mirroring ContactController::resolveAdminRecipients().
     *
     * @return Collection<int, User>
     */
    private function resolveAdminRecipients(): Collection
    {
        return User::query()->get()->filter(
            fn (User $user): bool => $user->is_admin
                || $user->can(Permissions::VIEW_REGISTRATION_REQUESTS)
                || $user->can(Permissions::MANAGE_REGISTRATION_REQUESTS)
        );
    }
}
