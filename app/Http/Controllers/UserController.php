<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
//package untuk autentikasi
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::WhereIn('role', ['admin', 'staff'])->get();
        return view('admin.user.index', compact('users'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ],[
            'first_name.required' => 'First name wajib diisi',
            'first_name.min' => 'Minimal 3 karakter',
            'last_name.required' => 'Last name wajib diisi',
            'last_name.min' => 'Minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter'
        ]);

        // User::create ->eloquent
        $createData = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        if ($createData) {
            return redirect()->route('login')->with('success','Berhasil membuat akun, silahkan login!');
        } return redirect()->route('signup')->with('failed', 'Gagal memproses data, silahkan coba lagi!');
        // redirect = untuk memindahkan/mengarahkan. with = mengirim data tambahan seperti pesan error
    }

    public function authentication(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ],[
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi'
        ]);
        // data yg digunakan untuk verifikasi
        $data = $request->only(['email','password']);
        if (Auth::attempt($data)) {
            // jika data email-pw cocok
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('success', 'Berhasil login');
            } return redirect()->route('home')->with('success', 'berhasil login!');
        } return redirect()->route('login')->with('error', 'Gagal! pastikan email dan password benar');
    }

    public function logout()
    {
        // logout() -> menghapus sesi login
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda telah logout! Silahkan login kembali untuk akses lengkap');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:dns|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);
        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'staff',
            'password' => Hash::make($request->password),
        ]);
        if ($createData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil menambahkan data baru');
        } return redirect()->back()->with('error', 'Gagal memproses data, silahkan coba lagi!');
    }

    public function datatables()
    {
        $users = User::query();
        // DataTables::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn() -> menambahkan column yang bukan bagian dari field movies, kbiasanya digunakan untuk button atau field yang nilainya akan diolah/ manipulasi
        // addIndexColumn() -> mengambil index data, mulai dari 1

        $users = User::WhereIn('role', ['admin', 'staff'])->get();

        return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('role_badge', function($user) {
            if ($user->role == 'admin') {
                return '<span class="badge badge-primary">' . $user->role . '</span>';
            } else {
                return '<span class="badge badge-success">' . $user->role . '</span>';
            }
        })
        ->addColumn('action', function ($user) {
            $btnEdit = '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.users.delete', $user->id) . '" method="POST">
            ' . @csrf_field() . method_field('DELETE') . ' <button type="submit" class="btn btn-danger">Hapus</button></form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })
        ->rawColumns(['role_badge', 'action'])
        ->make(true);
        // rawColumns() -> mendaftarkan column uang baru dibuat pada addColumn()

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:dns',
            'password' => 'nullable|min:8',
        ],
        [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.min' => 'Password minimal 8 karakter',
        ]);
        $updateData = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        if ($updateData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil mengubah data');
        } return redirect()->back()->with('error', 'Gagal memproses data, silahkan coba lagi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = 'data-pengguna.xlsx';
        return Excel::download(new UserExport, $fileName);
    }

    public function trash()
    {
        $userTrash = User::onlyTrashed()->get();
        return view('admin.user.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil mengembalikan data');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen');
    }
}
