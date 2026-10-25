<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

final class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        // The response is identical whether the account exists or not, so the
        // endpoint cannot be used to enumerate registered listeners.
        Password::sendResetLink($request->only('email'));

        return back()->with(
            'status',
            'Si el correo está registrado, recibirás un enlace para restablecer la contraseña.'
        );
    }
}
