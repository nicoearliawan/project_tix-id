<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
// proses memanipulasi waktu
use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // memunculkan data yang akan dimunculkan di excel
        return Movie::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ["No", 'Judul', 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster', 'Sinopsis', 'Status Aktif'];
    }

    // menentukan td
    public function map($movie): array
    {
        return [
            // menambah $key diatas dr 1 dst
            ++$this->key,
            $movie->title,

            // format ("i") : mengambil jam dari duration
            Carbon::parse($movie->duration)->format("H") . " Jam " .
            // format ("i") : mengambil menit dari duration
            Carbon::parse($movie->duration)->format("i") . " Menit",
            $movie->genre,
            $movie->director,
            $movie->age_rating . "+",
            // poster berupa url -> asset()
            asset("storage") .  "/" . $movie->poster,
            $movie->description,
            // jika actived 1 munculkan 'aktif', tidak muncul 'non-aktif'
            $movie->actived == 1 ? 'Aktif' : 'Non-Aktif',
        ];
    }
}
