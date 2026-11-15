<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            abort(400);
        }

        $request->session()->put('locale', $locale);

        return redirect()->back()->withCookie(cookie('aoi_locale', $locale, 60 * 24 * 365));
    }
}
