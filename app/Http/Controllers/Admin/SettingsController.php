<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);
        $user->update($data);
        return back()->with('success', __('settings.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        auth()->user()->update(['password' => $request->password]);
        return back()->with('success', __('settings.password_updated'));
    }

    public function updatePreferences(Request $request)
    {
        $data = $request->validate([
            'locale'  => ['required', 'in:ar,en'],
            'theme'   => ['required', 'in:light,dark'],
            'palette' => ['nullable', 'in:ocean,violet,teal,rose,amber'],
        ]);

        $request->session()->put('locale', $data['locale']);
        $request->session()->put('theme', $data['theme']);
        if (!empty($data['palette'])) {
            $request->session()->put('palette', $data['palette']);
        }

        // cookie so next request (and JS) can read without race
        $minutes = 60 * 24 * 365;
        cookie()->queue(cookie('aoi_theme', $data['theme'], $minutes));
        cookie()->queue(cookie('aoi_palette', $data['palette'] ?? 'ocean', $minutes));
        cookie()->queue(cookie('aoi_locale', $data['locale'], $minutes));

        return redirect()->back()
            ->with('success', __('settings.preferences_updated'))
            ->with('flash_theme', $data['theme'])
            ->with('flash_palette', $data['palette'] ?? 'ocean');
    }
}
