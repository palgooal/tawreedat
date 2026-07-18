<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Models\User;
use App\Notifications\NewContactRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ContactController extends Controller
{
    private const SUCCESS_MESSAGE = 'تم استلام طلبك بنجاح، وسيتواصل معك فريق توريد قريباً.';

    public function store(Request $request): RedirectResponse
    {
        // Honeypot: resources/views/pages/contact.blade.php renders a
        // "hp_check" field that's hidden from real visitors (off-screen,
        // aria-hidden, unreachable by tab, no label, unlabeled/nonsense
        // name so browsers have no autofill heuristic to match against -
        // a previous version named "website" with a real label was
        // getting silently autofilled by browsers, causing false-positive
        // spam detection on genuine submissions). A real submission never
        // has this field filled in. If it IS filled, this is almost
        // certainly a bot - pretend success (same message, same redirect)
        // so the bot learns nothing from the response, and skip validation
        // entirely so no validation error can leak back either. Nothing is
        // saved, no notification is sent.
        if (filled($request->input('hp_check'))) {
            return back()->with('success', self::SUCCESS_MESSAGE);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'inquiry_type' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactRequest = ContactRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'inquiry_type' => $validated['inquiry_type'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        $this->notifyAdmins($contactRequest);

        return back()->with('success', self::SUCCESS_MESSAGE);
    }

    /**
     * Notifying admins must never break the user-facing submission - the
     * inquiry is already safely saved by the time this runs, so any
     * failure here (bad MAIL_* config, SMTP timeout, etc.) is logged and
     * swallowed rather than surfaced or re-thrown.
     */
    private function notifyAdmins(ContactRequest $contactRequest): void
    {
        try {
            $recipients = $this->resolveAdminRecipients();

            if ($recipients->isEmpty()) {
                // Never fail if no admins exist yet (fresh install, no
                // roles assigned) - just skip notifying, the request is
                // still safely in the database either way.
                return;
            }

            Notification::send($recipients, new NewContactRequestNotification($contactRequest));
        } catch (Throwable $e) {
            Log::error('Failed to send new contact request notification.', [
                'contact_request_id' => $contactRequest->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Every user who can currently see contact requests in the admin panel
     * - permission-driven (`view contact requests` / `manage contact
     * requests`, see docs/ADMIN_PANEL.md), which today means Super Admin,
     * Admin, and Support - plus any legacy is_admin=true account, so a
     * fresh install with no roles assigned yet still notifies somebody
     * instead of silently notifying no one.
     *
     * @return Collection<int, User>
     */
    private function resolveAdminRecipients(): Collection
    {
        return User::query()->get()->filter(
            fn (User $user): bool => $user->is_admin
                || $user->can('view contact requests')
                || $user->can('manage contact requests')
        );
    }
}
