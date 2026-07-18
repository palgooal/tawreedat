<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show any CMS page by slug (e.g. /p/privacy, /p/terms).
     */
    public function show(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOr(function () {
                abort(404);
            });

        return $this->render($page);
    }

    /**
     * "About" is a fixed, well-known slug backed by the same pages table.
     */
    public function about(): View
    {
        return $this->showBySlug('about');
    }

    /**
     * "Plans" is a fixed, well-known slug backed by the same pages table.
     */
    public function plans(): View
    {
        return $this->showBySlug('plans');
    }

    private function showBySlug(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOr(function () {
                abort(404);
            });

        return $this->render($page);
    }

    private function render(Page $page): View
    {
        return view('pages.show', [
            'page' => $page,
            'seoTitle' => $page->seo_title ?: $page->title.' | توريد',
            'seoDescription' => $page->seo_description ?: $page->excerpt,
        ]);
    }
}
