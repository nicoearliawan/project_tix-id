<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // memunculkan data yang akan dimunculkan di excel
        return User::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ["No", 'Nama', 'Email', 'Role', 'Tanggal Bergabung'];
    }

    // menentukan td
    public function map($user): array
    {
        return [
            // menambah $key diatas dr 1 dst
            ++$this->key,
            $user->name,
            $user->email,
            $user->role,
            $user->created_at->format('d-m-Y'),
        ];
    }
}
