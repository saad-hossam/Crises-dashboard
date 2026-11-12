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

        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ])->save();

        return back()->with('success', __('settings.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->password = $request->password;
        $user->save();

        return back()->with('success', __('settings.password_updated'));
    }

    public function updatePreferences(Request $request)
    {
        $data = $request->validate([
            'locale'  => ['required', 'in:ar,en'],
            'theme'   => ['required', 'in:light,dark'],
            'palette' => ['nullable', 'in:navy,graphite,forest,steel,wine'],
        ]);

        $palette = $data['palette'] ?? 'navy';

        $request->session()->put('locale', $data['locale']);
        $request->session()->put('theme', $data['theme']);
        $request->session()->put('palette', $palette);

        $minutes = 60 * 24 * 365;

        return redirect()
            ->route('admin.settings.index')
            ->with('success', __('settings.preferences_updated'))
            ->with('flash_theme', $data['theme'])
            ->with('flash_palette', $palette)
            ->withCookie(cookie('aoi_locale', $data['locale'], $minutes))
            ->withCookie(cookie('aoi_theme', $data['theme'], $minutes))
            ->withCookie(cookie('aoi_palette', $palette, $minutes));
    }
}
