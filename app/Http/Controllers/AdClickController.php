<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;

class AdClickController extends Controller
{
    /**
     * Every advertisement link on the site points here first
     * (route('ads.click', $advertisement)) so a click can be counted before
     * the visitor is sent on to the real destination. If the ad has no link
     * (or was removed), fall back to the homepage rather than 404ing a
     * visitor who clicked a real banner.
     */
    public function __invoke(Advertisement $advertisement): RedirectResponse
    {
        $advertisement->increment('clicks');

        return redirect()->away($advertisement->link ?: route('home'));
    }
}
