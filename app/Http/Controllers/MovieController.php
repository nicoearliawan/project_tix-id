<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function chart()
    {
        $filmActive = Movie::where('actived', 1)->count(); //yg diperlukan hanya jumlah, count()
        $filmNonActive = Movie::where('actived', 0)->count();
        $data = [$filmActive, $filmNonActive];
        return response()->json([
            'data' => $data,
        ]);
    }

    public function datatables()
    {
        $movies = Movie::query();
        // DataTables::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn() -> menambahkan column yang bukan bagian dari field movies, kbiasanya digunakan untuk button atau field yang nilainya akan diolah/ manipulasi
        // addIndexColumn() -> mengambil index data, mulai dari 1
        return DataTables::of($movies)
        ->addIndexColumn()
        ->addColumn('poster_img', function($movie) {
            $url = asset('storage/' . $movie->poster);
            return '<img src="' . $url . '" width="70">';
        })
        ->addColumn('actived_badge', function($movie) {
            if ($movie->actived) {
                return '<span class="badge badge-success">Aktif</span>';
            } else {
                return '<span class="badge badge-danger">Nonaktif</span>';
            }
        })
        ->addColumn('action', function ($movie) {
            $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal(' . $movie . ')\'>Detail</button>';
            $btnEdit = '<a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.movies.delete', $movie->id) . '" method="POST">
            ' . @csrf_field() . method_field('DELETE') . ' <button type="submit" class="btn btn-danger">Hapus</button></form>';
            $btnNonAktif = '';
            if ($movie->actived) {
                $btnNonAktif = '<form action="' . route('admin.movies.patch', $movie->id) . '" method="POST" class="me-2">' . @csrf_field() . method_field('PATCH') . ' <button type="submit" class="btn btn-warning">Non-aktif</button></form>';
            }
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
        })
        ->rawColumns(['poster_img', 'actived_badge', 'action'])
        ->make(true);
        // rawColumns() -> mendaftarkan column uang baru dibuat pada addColumn()
    }

    public function home() {
        // where('field', 'operator', 'value') : mencari data
        // operator : = / < / <= / > / >= / <> / !=
        // orderBy('field', 'ASC/DESC') : mengurutkan data
        // ASC : a-z, 0-9, terlama-terbaru, DESC : 9-0, z-a, terbaru-terima
        // limit(angka) : mengambil hanya beberapa data
        // get() : ambil hasil proses filter
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->limit(3)
        ->get();
        // get() mengambil data di filter
        return view('home', compact('movies'));
    }

     public function homeMovies(Request $request)
     {
        // ambil request dari input search
        $nameMovie = $request->search_movie;
        // cek jika input name="search_movie" tidak kosong
        if ($nameMovie != "") {
            // LIKE : mencari data yang mengandung teks tertentu
            // % di depan : mencari kata belakang, % di belakang : mencari data di depan, % depan belakang : mencari di depan tengah belakang
            // where(), orderBy(), get() adalah eloquent method
            $movies = Movie::where('title', 'LIKE', '%'.$nameMovie.'%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('movies', compact('movies'));
    }

    public function movieSchedule(Request $request, $movie_id)
    {
        $sortPrice = $request->sort_price;

        if ($sortPrice) {
            $movie = Movie::where('id', $movie_id)->with(['schedules' =>function($q) use ($sortPrice)
            {
                // karna mau mengurutkan price, price di schedules. schedules itu ada di relasi jadi gunakan fungsi anonim
                // $q = query eloquent, mewakilkan model relasi (model schedule)
                $q->orderBy('price', $sortPrice);
            }, 'schedules.cinema'])->first();


        } else {
            // ambil data movie bersama schedule dan cinema
            // karna cinema adanya relasi dengan schedule bukan movie, jadi gunakan schedule.cinema
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }

        $sortAlphabet = $request->sort_alphabet;
        if ($sortAlphabet == 'ASC') {
            $movie->schedules = $movie->schedules->sortBy(function($schedule) {
                return $schedule->cinema->name;
            })->values();
        } elseif ($sortAlphabet == 'DESC') {
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule) {
                return $schedule->cinema->name;
            })->values();
        }


        // schedules : mengambil relasi schedules
        // schedules.cinema : ambil relasi cinema dari schedules
        // first() : mengambil 1 data film
        return view('schedule.detail', compact('movie'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title'=> 'required',
            'duration'=> 'required',
            'genre'=> 'required',
            'director'=> 'required',
            'age_rating'=> 'required',
            //mimes memastikan ekstensi(jenis file) yg diupload
            'poster' => 'required|mimes:jpg,jpeg,png,svg,webp',
            'description'=> 'required|min:10',
        ], [
            'title.required'=> 'Judul Film harus diisi',
            'duration.required'=> 'Durasi Film harus diisi',
            'genre.required'=> 'Genre Film harus diisi',
            'director.required'=> 'Sutradara Film harus diisi',
            'age_rating.required'=> 'Usia Minimal harus diisi',
            'poster.required'=> 'Poster Film harus diisi',
            'poster.mimes'=> 'Poster harus berupa JPG/JPEG/PNG/SVG/WEBP',
            'description.required'=> 'Sinopsis harus diisi',
            'description.min'=> 'Sinopsis harus diisi minimal 10 karakter',
        ]);
        // ambil file dari input : $request->file('name_input')
        $poster = $request->file('poster');
        // buat nama baru untuk file nya
        //format file baru yg diharapkan : <acak>-poster.jpg
        // getClientOriginalExtension() : mengambil ekstensi file yang diupload
        $namaFile = Str::random(10) . "-poster." . $poster->getClientOriginalExtension();
        // simpan file ke folder storage :storeAs("namasubfolder",namafile,"visibilty")
        $path = $poster->storeAs("poster", $namaFile, "public");
        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // poster diisi dgn hasil storeAs(), hasil penyimpanan file di storage sebelumnya
            'poster' => $path,
            'description' => $request->description,
            'actived' => 1
        ]);
        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil Menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Silahkan coba lagi!');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie, $id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie, $id)
    {
         $request->validate([
            'title'=> 'required',
            'duration'=> 'required',
            'genre'=> 'required',
            'director'=> 'required',
            'age_rating'=> 'required',
            //mimes memastikan ekstensi(jenis file) yg diupload
            'poster' => 'mimes:jpg,jpeg,png,svg,webp',
            'description'=> 'required|min:10',
        ], [
            'title.required'=> 'Judul Film harus diisi',
            'duration.required'=> 'Durasi Film harus diisi',
            'genre.required'=> 'Genre Film harus diisi',
            'director.required'=> 'Sutradara Film harus diisi',
            'age_rating.required'=> 'Usia Minimal harus diisi',
            'poster.mimes'=> 'Poster harus berupa JPG/JPEG/PNG/SVG/WEBP',
            'description.required'=> 'Sinopsis harus diisi',
            'description.min'=> 'Sinopsis harus diisi minimal 10 karakter',
        ]);
        // ambil data sebelumnya
        $movie = Movie::find($id);

        // jika input file poster diisi
        if ($request->hasFile('poster')) {
            $filePath = storage_path('app/public/' . $movie->poster);
            // jika file ada di storage path tersebut
            if(file_exists($filePath)) {
                // untuk menghapus file lama
                unlink($filePath);
            }
            $file = $request->file('poster');
            // buat nama baru untuk file
            $fileName= 'poster-' . Str::random(10). "." .
            $file->getClientOriginalExtension();
            $path = $file->storeAs('poster', $fileName, 'public');
        }

        $updateData = $movie->update([
            'title' => $request->title,
             'duration' => $request->duration,
              'genre' => $request->genre,
               'director' => $request->director,
             'age_rating' => $request->age_rating,
              'poster' => $request->hasFile('poster') ? $path : $movie->poster,
               'description' => $request->description,
                'actived' => 1 ,
        ]);

        if($updateData) {
            return redirect()->route('admin.movies.index')->with('success',
            'Berhasil memperbarui detail!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie, $id)
    {
        $schedules = Schedule::where('movie_id', $id)->count();
        if ($schedules)
            {
            return redirect()->route('admin.movies.index')->with('error', 'Tidak dapat menghapus data film! Data tertaut dengan jadwal tayang.');
            }

       $movie = Movie::findOrFail($id);

    // hapus data dari database
    $movie->delete();
           return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data!');
    }

    public function patch($id)
    {
    $movie = Movie::findOrFail($id);

    // kalau sekarang aktif, ubah ke nonaktif. kalau nonaktif, ubah ke aktif
    $movie->actived = $movie->actived ? 0 : 1;
    $movie->save();

    return redirect()->route('admin.movies.index')->with('success',
    'Status film berhasil diubah!');
    }


    public function export()
    {
        // nama file yg akan di download
        // extensi antara xlsx/csv
        $fileName = 'data-film.xlsx';
        // proses download
        return Excel::download(new MovieExport, $fileName);
    }

    public function trash()
    {
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengembalikan data');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);

        // hapus file poster jika ada
        $fileDel = storage_path('app/public/' . $movie->poster);
        if ($movie->poster && file_exists($fileDel))
            {
                unlink($fileDel);
            }

        $movie->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen');
    }
}
