@extends('Templates.app')
@section('content')

<div class="card w-50 d-block mx-auto my-5 p-4">
    <div class="card-body">
        <h5 class="text-center">Selesaikan Pembayaran</h5>
        <img src="{{ asset('storage/' . $ticket['ticketPayment']['qrcode'])}}" alt="" class="d-block mx-auto mb-3" style="width: 200px">
        <table class="w-100">
            <tr>
                <td>{{ $ticket['quantity'] }} Tiket</td>
                <td><b>{{ implode(',' , $ticket['rows_of_seats']) }}</b></td>
            </tr>
            <tr>
                <td>Harga Tiket</td>
                <td><b>Rp. {{ number_format($ticket['schedule']['price']) }} <span class="text-secondary">X{{ $ticket['quantity'] }}</span></b></td>
            </tr>
            <tr>
                <td>Biaya Layanan</td>
                <td><b>Rp. 4.000 <span class="text-secondary">X{{ $ticket['quantity'] }}</span></b></td>
            </tr>
            <tr>
                <td>Promo</td>
                @if ($ticket['promo'])
                <td><b>{{ $ticket['promo']['type'] == 'percent' ? $ticket['promo']['discount'] . '%' : 'Rp. ' . number_format($ticket['promo']['discount'], 0, ',', '.') }}</b></td>
                @else
                <td><b>-</b></td>
                @endif
            </tr>
        </table>
        <hr>
        <div class="d-flex justify-content-end mb-3">
            @php
                $price = $ticket['total_price'] + $ticket['tax'];
            @endphp
            <b>Total Rp. {{ number_format($price, 0, ',', '.') }}</b>
        </div>
        <form action="{{ route('tickets.payment.status', $ticket['id']) }}" method="POST">
            @csrf
            @method('PATCH')
            <button class="btn btn-lg btn-block btn-primary">Sudah Dibayar</button>
        </form>
    </div>
</div>

@endsection
