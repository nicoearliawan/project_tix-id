<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Schedule extends Model
{
    //
    use softDeletes;

    protected $fillable = ['cinema_id','movie_id','hours','price'];

    protected function casts() : array
    {
        return [
            // mengubah format json migration hours menjadi array
            'hours' => 'array',
        ];
    }

    // schedule pegang posisi kedua, panggil relasi dengan belongsTo
    // cinema pegang posisi pertama dan jenis (one) jd gunakan tunggal

    public function cinema() {
        return $this->belongsTo(Cinema::class);
    }

    public function movie() {
        return $this->belongsTo(Movie::class);
    }
}
