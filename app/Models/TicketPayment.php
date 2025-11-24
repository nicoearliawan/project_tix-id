<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;


class TicketPayment extends Model
{
    use softDeletes;

    protected $fillable = ['ticket_id', 'qrcode', 'booked_date', 'paid_date'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
