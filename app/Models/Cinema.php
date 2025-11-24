<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Cinema extends Model
{
    //mendaftarkan softDeletes
    use softDeletes;

    //mendaftarkan column yg akan diisi oleh pengguna (column migration selain id dan timestamps)
    protected $fillable = ['name', 'location'];

    // karena cinema pegang posisi pertama (one to many: cinema dan schedules)
    // mendaftarkan jenis relasi
    // nama relasi tunggal/jamak tergantung jenisnya. schedules (many) jamak

    public function schedules() {
        // one to one: hasOne
        // one to many: hasMany

        return $this->hasMany(Schedules::class);
    }
}
