<?php

namespace App\Http\Controllers\Auth;

use App\Application\Account\EmailAlreadyRegistered;
use App\Application\Account\RegisterListener;
use App\Application\Account\RegisterListenerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, RegisterListener $register): RedirectResponse
    {
        try {
            $account = $register->execute(new RegisterListenerData(
                name: (string) $request->string('name'),
                email: (string) $request->string('email'),
                password: (string) $request->string('password'),
            ));
        } catch (EmailAlreadyRegistered|UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'email' => 'Este correo ya está registrado.',
            ]);
        }

        Auth::guard('web')->loginUsingId($account->id);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Tu cuenta fue creada. Ya puedes escuchar la radio.');
    }
}
