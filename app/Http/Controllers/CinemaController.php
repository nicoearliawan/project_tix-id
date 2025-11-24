<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CinemaExport;
use Yajra\DataTables\Facades\DataTables;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Model::all() -> mengambil semua data di model
        $cinemas = Cinema::all();
        // compat() -> mengirim data ke blade, nama compact sama dengan nama variable
        return view('admin.cinema.index', compact('cinemas'));
    }

    public function datatables()
    {
        $cinemas = Cinema::query();
        // DataTables::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn() -> menambahkan column yang bukan bagian dari field movies, kbiasanya digunakan untuk button atau field yang nilainya akan diolah/ manipulasi
        // addIndexColumn() -> mengambil index data, mulai dari 1
        return DataTables::of($cinemas)
        ->addIndexColumn()
        ->addColumn('action', function ($cinema) {
            $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.cinemas.delete', $cinema->id) . '" method="POST">
            ' . @csrf_field() . method_field('DELETE') . ' <button type="submit" class="btn btn-danger">Hapus</button></form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })
        ->rawColumns(['action'])
        ->make(true);
        // rawColumns() -> mendaftarkan column uang baru dibuat pada addColumn()

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cinema.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama bioskop harus diisi',
            'location.required' => 'Lokasi harus diisi',
            'location.min' => 'Lokasi harus diisi minimal 10 karakter',
        ]);
        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        if ($createData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil menambahkan data baru');
        } return redirect()->back()->with('error', 'Gagal memproses data, silahkan coba lagi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // edit($id) => $id dari {id} di route edit
        $cinema = Cinema::find($id);
        // dd() => cek data
        // dd($cinema->toArray());
        return view('admin.cinema.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //(Request $request, $id) : Request $request (ambil data form), $id ambil parameter placeholder {id} dari route
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10'
        ],[
            'name.required' => 'Nama Bioskop harus diisi',
            'location.required' => 'Lokasi Bioskop harus diisi',
            'location.min' => 'Lokasi Bioskop harus diisi minaml 10 karakter',
        ]);
        $updateData = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        // where('id', $id) -> sebelum diupdate wajib cari datanya. Untuk mencarinya salah satunya dengan cara where
        // format -> where('field_di_fillable', $sumberData)
        if ($updateData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengubah data!');
        } return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // count : menghitung jumlah data yang didapat
        $schedules = Schedule::where('cinema_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.cinemas.index')->with('error', 'Tidak dapat menghapus data bioskop! Data tertaut dengan jadwal tayang.');
        }
        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = 'data-bioskop.xlsx';
        return Excel::download(new CinemaExport, $fileName);
    }

    public function trash()
    {
        $cinemaTrash = Cinema::onlyTrashed()->get();
        return view('admin.cinema.trash', compact('cinemaTrash'));
    }

    public function restore($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->restore();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengembalikan data');
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen');
    }

    public function cinemaList()
    {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }

    public function cinemaSchedules($cinema_id) {
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function($q) {
            $q->where('actived', 1);
        })->get();
        return view('schedule.cinema-schedules', compact('schedules'));
    }
}

