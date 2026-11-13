<?php

namespace App\Http\Controllers\Admin;

use App\Application\Admin\AdminAccount;
use App\Application\Admin\AdminAccountRepository;
use App\Application\Admin\AdminAlreadyExists;
use App\Application\Admin\AdministratorNotRemovable;
use App\Application\Admin\CreateAdministrator;
use App\Application\Admin\DeleteAdministrator;
use App\Application\Admin\InvalidAdministratorPassword;
use App\Application\Admin\UpdateAdministrator;
use App\Application\Cms\AdminActor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminAccountRequest;
use App\Http\Requests\Admin\UpdateAdminAccountRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminUserController extends Controller
{
    public function index(AdminAccountRepository $admins): View
    {
        return view('admin.accounts.index', [
            'accounts' => $admins->all(),
            'currentId' => (int) request()->user('admin')?->getKey(),
        ]);
    }

    public function create(): View
    {
        return view('admin.accounts.form', [
            'account' => null,
        ]);
    }

    public function store(
        StoreAdminAccountRequest $request,
        CreateAdministrator $create,
    ): RedirectResponse {
        try {
            $create->handle(
                name: (string) $request->validated('name'),
                email: (string) $request->validated('email'),
                plainPassword: (string) $request->validated('password'),
                actor: $this->actor($request),
            );
        } catch (AdminAlreadyExists|InvalidAdministratorPassword $exception) {
            return back()->withInput()->withErrors(['email' => $exception->getMessage()]);
        }

        return redirect()->route('admin.accounts.index')->with('status', 'Cuenta administrativa creada.');
    }

    public function edit(int $account, AdminAccountRepository $admins): View
    {
        return view('admin.accounts.form', [
            'account' => $this->find($account, $admins),
        ]);
    }

    public function update(
        UpdateAdminAccountRequest $request,
        int $account,
        AdminAccountRepository $admins,
        UpdateAdministrator $update,
    ): RedirectResponse {
        $existing = $this->find($account, $admins);
        $password = trim((string) $request->validated('password'));

        try {
            $update->handle(
                account: $existing,
                name: (string) $request->validated('name'),
                email: (string) $request->validated('email'),
                plainPassword: $password !== '' ? $password : null,
                actor: $this->actor($request),
            );
        } catch (AdminAlreadyExists|InvalidAdministratorPassword $exception) {
            return back()->withInput()->withErrors(['email' => $exception->getMessage()]);
        }

        if ($password !== '' && $existing->id === (int) $request->user('admin')?->getKey()) {
            $request->session()->regenerate();
        }

        return redirect()->route('admin.accounts.index')->with('status', 'Cuenta administrativa actualizada.');
    }

    public function confirmDelete(int $account, AdminAccountRepository $admins): View
    {
        return view('admin.accounts.delete', [
            'account' => $this->find($account, $admins),
            'isCurrent' => $account === (int) request()->user('admin')?->getKey(),
            'accountCount' => $admins->count(),
        ]);
    }

    public function destroy(
        int $account,
        AdminAccountRepository $admins,
        DeleteAdministrator $delete,
        Request $request,
    ): RedirectResponse {
        $existing = $this->find($account, $admins);

        try {
            $delete->handle(
                $existing,
                (int) $request->user('admin')?->getKey(),
                $this->actor($request),
            );
        } catch (AdministratorNotRemovable $exception) {
            return redirect()
                ->route('admin.accounts.index')
                ->withErrors(['account' => $exception->getMessage()]);
        }

        return redirect()->route('admin.accounts.index')->with('status', 'Cuenta administrativa eliminada.');
    }

    private function find(int $id, AdminAccountRepository $admins): AdminAccount
    {
        return $admins->find($id) ?? abort(404);
    }

    private function actor(Request $request): AdminActor
    {
        return AdminActor::fromAdmin($request->user('admin'));
    }
}
