<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /* =========================================================
     * PPIC
     * =======================================================*/
    public function showPPIC()
    {
        $users = User::where('role', 'PPIC')
            ->orderBy('name')
            ->get();

        return view('users_management.show_ppic', compact('users'));
    }

    public function storePPIC(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:7'],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => 'PPIC',
            'email_verified_at' => now(),
            'password'          => Hash::make($request->password),
        ]);

        return redirect()
            ->route('show-ppic')
            ->with('success', 'Akun PPIC berhasil dibuat.');
    }

    public function editPPIC($id)
    {
        $ppic = User::where('id', $id)
            ->where('role', 'PPIC')
            ->firstOrFail();

        return view('users_management.edit_ppic', compact('ppic'));
    }

    public function updatePPIC(Request $request, $id)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable', 'min:7'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $id)
            ->where('role', 'PPIC')
            ->update($data);

        return redirect()
            ->route('show-ppic')
            ->with('success', 'Akun PPIC diperbarui.');
    }

    /* =========================================================
     * PRODUKSI
     * (biarkan tetap ada kalau masih dipakai)
     * =======================================================*/
    public function showProduksi()
    {
        $users = User::where('role', 'Produksi')
            ->orderBy('name')
            ->get();

        return view('users_management.show_produksi', compact('users'));
    }

    public function storeProduksi(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:7'],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => 'Produksi',
            'email_verified_at' => now(),
            'password'          => Hash::make($request->password),
        ]);

        return redirect()
            ->route('show-produksi')
            ->with('success', 'Akun Produksi berhasil dibuat.');
    }

    public function editProduksi($id)
    {
        $produksi = User::where('id', $id)
            ->where('role', 'Produksi')
            ->firstOrFail();

        return view('users_management.edit_produksi', compact('produksi'));
    }

    public function updateProduksi(Request $request, $id)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable', 'min:7'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $id)
            ->where('role', 'Produksi')
            ->update($data);

        return redirect()
            ->route('show-produksi')
            ->with('success', 'Akun Produksi diperbarui.');
    }

    /* =========================================================
     * QC
     * =======================================================*/
    public function showQC()
    {
        $users = User::where('role', 'QC')
            ->orderBy('name')
            ->get();

        return view('users_management.show_qc', compact('users'));
    }

    public function storeQC(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:7'],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => 'QC',
            'email_verified_at' => now(),
            'password'          => Hash::make($request->password),
        ]);

        return redirect()
            ->route('show-qc')
            ->with('success', 'Akun QC berhasil dibuat.');
    }

    public function editQC($id)
    {
        $qc = User::where('id', $id)
            ->where('role', 'QC')
            ->firstOrFail();

        return view('users_management.edit_qc', compact('qc'));
    }

    public function updateQC(Request $request, $id)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable', 'min:7'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $id)
            ->where('role', 'QC')
            ->update($data);

        return redirect()
            ->route('show-qc')
            ->with('success', 'Akun QC diperbarui.');
    }

    /* =========================================================
     * QA
     * =======================================================*/
    public function showQA()
    {
        $users = User::where('role', 'QA')
            ->orderBy('name')
            ->get();

        return view('users_management.show_qa', compact('users'));
    }

    public function storeQA(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:7'],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => 'QA',
            'email_verified_at' => now(),
            'password'          => Hash::make($request->password),
        ]);

        return redirect()
            ->route('show-qa')
            ->with('success', 'Akun QA berhasil dibuat.');
    }

    public function editQA($id)
    {
        $qa = User::where('id', $id)
            ->where('role', 'QA')
            ->firstOrFail();

        return view('users_management.edit_qa', compact('qa'));
    }

    public function updateQA(Request $request, $id)
    {
        $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable', 'min:7'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $id)
            ->where('role', 'QA')
            ->update($data);

        return redirect()
            ->route('show-qa')
            ->with('success', 'Akun QA diperbarui.');
    }

    /* =========================================================
     * LOGIN SEBAGAI PPIC (impersonate)
     * =======================================================*/
    public function loginAsPPIC($id)
    {
        // pastikan user yang dituju benar-benar PPIC
        $ppic = User::where('id', $id)
            ->where('role', 'PPIC')
            ->firstOrFail();

        // simpan id admin yang lagi login (opsional, kalau mau nanti "kembali sebagai admin")
        if (! session()->has('impersonator_id')) {
            session(['impersonator_id' => Auth::id()]);
        }

        Auth::login($ppic);

        // sesuaikan redirect-nya kalau ada dashboard khusus PPIC
        return redirect()->route('dashboard')->with('success', 'Sekarang login sebagai PPIC: ' . $ppic->name);
    }

    /* =========================================================
     * COMMON
     * =======================================================*/
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $role = $user->role;
        $user->delete();

        if ($role === 'Produksi') {
            return redirect()->route('show-produksi')->with('success', 'Akun Produksi dihapus.');
        }
        if ($role === 'QC') {
            return redirect()->route('show-qc')->with('success', 'Akun QC dihapus.');
        }
        if ($role === 'QA') {
            return redirect()->route('show-qa')->with('success', 'Akun QA dihapus.');
        }
        if ($role === 'PPIC') {
            return redirect()->route('show-ppic')->with('success', 'Akun PPIC dihapus.');
        }

        // fallback
        return redirect()->route('dashboard')->with('success', 'Akun dihapus.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('users_management.setting_profile', compact('user'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email,' . Auth::id()],
        ]);

        User::where('id', Auth::id())->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('show-profile')->with('success', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password'         => ['required', 'min:7'],
            'confirm_new_password' => ['required', 'same:new_password'],
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('show-profile')->with('success', 'Password diganti.');
    }

    /* =========================================================
     * Alias kompatibilitas lama (opsional)
     * =======================================================*/
    public function showSiswa()                  { return $this->showProduksi(); }
    public function storeSiswa(Request $r)       { return $this->storeProduksi($r); }
    public function editSiswa($id)               { return $this->editProduksi($id); }
    public function updateSiswa(Request $r, $id) { return $this->updateProduksi($r, $id); }
}
