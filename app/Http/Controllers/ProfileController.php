<?php

namespace App\Http\Controllers;

use App\Application\Account\ChangeListenerPassword;
use App\Application\Account\EmailAlreadyRegistered;
use App\Application\Account\InvalidCurrentPassword;
use App\Application\Account\UpdateListenerProfile;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user('web'),
        ]);
    }

    public function update(UpdateProfileRequest $request, UpdateListenerProfile $update): RedirectResponse
    {
        try {
            $update->execute(
                listenerId: (int) $request->user('web')->getKey(),
                name: (string) $request->string('name'),
                email: (string) $request->string('email'),
            );
        } catch (EmailAlreadyRegistered) {
            throw ValidationException::withMessages([
                'email' => 'Este correo ya está registrado.',
            ]);
        }

        return back()->with('status', 'Perfil actualizado.');
    }

    public function updatePassword(
        UpdatePasswordRequest $request,
        ChangeListenerPassword $change,
    ): RedirectResponse {
        try {
            $change->execute(
                listenerId: (int) $request->user('web')->getKey(),
                currentPassword: (string) $request->string('current_password'),
                newPassword: (string) $request->string('password'),
            );
        } catch (InvalidCurrentPassword) {
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña actual no coincide.',
            ]);
        }

        $request->session()->regenerate();

        return back()->with('status', 'Contraseña actualizada.');
    }
}
