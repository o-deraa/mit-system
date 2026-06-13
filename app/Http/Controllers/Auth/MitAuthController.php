<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WebAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MitAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request, WebAuthService $auth): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,warga,maba'],
            'nrp' => ['nullable', 'string', 'max:50'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ]);

        $role = $validated['role'];

        if ($role === 'admin') {
            $admin = $auth->loginAdmin(
                (string) $request->input('username'),
                (string) $request->input('password')
            );

            if (!$admin) {
                return back()
                    ->withInput($request->except('password'))
                    ->with('error', 'Login admin gagal. Username atau password salah.');
            }

            session([
                'mit_role' => 'admin',
                'mit_user_id' => $admin['id'],
                'mit_user_name' => $admin['name'],
                'mit_admin_identifier' => $admin['identifier'],
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Login admin berhasil.');
        }

        if ($role === 'warga') {
            $warga = $auth->loginWarga(
                (string) $request->input('nrp'),
                (string) $request->input('password')
            );

            if (!$warga) {
                return back()
                    ->withInput($request->except('password'))
                    ->with('error', 'Login warga gagal. NRP/password salah atau akun inactive.');
            }

            session([
                'mit_role' => 'warga',
                'mit_user_id' => $warga->warga_id,
                'mit_user_name' => $warga->nama,
            ]);

            return redirect()->route('warga.dashboard')
                ->with('success', 'Login warga berhasil.');
        }

        if ($role === 'maba') {
            $maba = $auth->loginMaba(
                (string) $request->input('nrp'),
                (string) $request->input('password')
            );

            if (!$maba) {
                return back()
                    ->withInput($request->except('password'))
                    ->with('error', 'Login maba gagal. NRP/password salah atau akun inactive.');
            }

            session([
                'mit_role' => 'maba',
                'mit_user_id' => $maba->maba_id,
                'mit_user_name' => $maba->nama,
            ]);

            return redirect()->route('maba.dashboard')
                ->with('success', 'Login maba berhasil.');
        }

        return back()->with('error', 'Role tidak valid.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'mit_role',
            'mit_user_id',
            'mit_user_name',
            'mit_admin_identifier',
        ]);

        $request->session()->regenerateToken();

        return redirect()
            ->route('mit.login')
            ->with('success', 'Logout berhasil.');
    }
}
