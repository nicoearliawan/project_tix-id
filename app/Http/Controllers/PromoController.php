<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromoExport;
use Yajra\DataTables\Facades\DataTables;


class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    public function datatables()
    {
        $promos = Promo::query();
        // DataTables::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn() -> menambahkan column yang bukan bagian dari field movies, kbiasanya digunakan untuk button atau field yang nilainya akan diolah/ manipulasi
        // addIndexColumn() -> mengambil index data, mulai dari 1
        return DataTables::of($promos)
        ->addIndexColumn()

        ->addColumn('type', function($promo) {
            if ($promo->type === 'rupiah') {
                return 'Rp.' . number_format($promo->discount, 0 , ',','.');
            } else {
                return $promo->discount . '%';
            }
        })
        ->addColumn('action', function ($promo) {
            $btnEdit = '<a href="' . route('staff.promos.edit', $promo->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('staff.promos.delete', $promo->id) . '" method="POST">
            ' . @csrf_field() . method_field('DELETE') . ' <button type="submit" class="btn btn-danger">Hapus</button></form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })
        ->rawColumns(['type', 'action'])
        ->make(true);
        // rawColumns() -> mendaftarkan column uang baru dibuat pada addColumn()

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required',
            'type' => 'required|in:rupiah,percent',
            'discount' => $request->type === 'rupiah'
                ? 'required|numeric|min:500'
                : 'required|numeric|min:0|max:100',
        ], [
            'promo_code.required' => 'Kode promo wajib diisi',
            'type.required' => 'Tipe diskon wajib diisi',
            'type.in' => 'Tipe diskon harus berupa "rupiah" atau "percent"',
            'discount.required' => 'Total potongan wajib diisi',
            'discount.numeric' => 'Diskon harus berupa angka',
            'discount.min' => 'Diskon dalam rupiah minimal Rp 500',
            'discount.max' => 'Diskon dalam persen maksimal 100%',
        ]);
        $createData = Promo::create([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => 1
        ]);
        if ($createData) {
            // redirect untuk mengarahkan ke route, with adalah untuk memberikan pesan
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil menambahkan data!');
        }   return redirect()->back()->with('error', 'Gagal menambahkan data! Silahkan coba lagi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo, $id)
    {
        $promo = Promo::find($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo, $id)
    {
        $request->validate([
            'promo_code' => 'required',
            'type' => 'required|in:rupiah,percent',
            'discount' => $request->type === 'rupiah'
                ? 'required|numeric|min:500'
                : 'required|numeric|min:0|max:100',
        ], [
            'promo_code.required' => 'Kode promo wajib diisi',
            'type.required' => 'Tipe diskon wajib diisi',
            'type.in' => 'Tipe diskon harus berupa "rupiah" atau "percent"',
            'discount.required' => 'Total potongan wajib diisi',
            'discount.numeric' => 'Diskon harus berupa angka',
            'discount.min' => 'Diskon dalam rupiah minimal Rp 500',
            'discount.max' => 'Diskon dalam persen maksimal 100%',
        ]);
        $promo = Promo::find($id);

        $updateData = $promo->update([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => 1
        ]);
        if ($updateData) {
            // redirect untuk mengarahkan ke route, with adalah untuk memberikan pesan
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil menambahkan data!');
        }   return redirect()->back()->with('error', 'Gagal menambahkan data! Silahkan coba lagi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo, $id)
    {
        Promo::where('id', $id)->delete();
        return redirect()->route('staff.promos.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = 'data-promo.xlsx';
        return Excel::download(new PromoExport, $fileName);
    }

    public function trash()
    {
        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));
    }

    public function restore($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->restore();
        return redirect()->route('staff.promos.index')->with('success', 'Berhasil mengembalikan data');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen');
    }
}
