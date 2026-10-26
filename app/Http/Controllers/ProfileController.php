<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update basic info
        $user->nama = $request->validated('nama');
        $user->email = $request->validated('email');

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = $request->validated('password');
        }

        // Save changes (password will be automatically hashed by cast)
        $user->save();

        // Update the password hash in session so session remains active
        if ($request->filled('password')) {
            $request->session()->put('password_hash_web', $user->getAuthPassword());
        }

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
