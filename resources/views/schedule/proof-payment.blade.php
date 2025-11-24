@extends('Templates.app')

@section('content')
<div class="container card w-75 d-block mx-auto text-center mt-4 p-4">
    <div class="card-body">
        <div class="d-flex justify-content-ennd mb-3">
            <a href="{{ route('tickets.export_pdf', $ticket->id) }}" class="btn btn-secondary">Download PDF</a>
        </div>
        <div class="d-flex flex-warp justify-content center">
            @foreach ($ticket['rows_of_seats'] as $item)
            <div class="my-3 mx-5">
                <div class="d-flex justify-content-between">
                    <div><b>
                        {{ $ticket['schedule']['cinema']['name'] }}
                        </b>
                    </div>
                    <div>
                        <h5 class="m-0"> Studio</h5>
                    </div>
                </div>
                <hr>
                <div class="ticket-body text-start">
                    <p class="ticket-title mb-1">{{ $ticket['schedule']['movie']['title'] }}</p>
                    <div class="ticket-detail">
                        <small>Tanggal:</small>{{
                            \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('d F, Y ') }}<br>
                            <small>Waktu:</small>{{ \Carbon\Carbon::parse($ticket['hours'])->format('H:i') }}<br>
                            <small>Kursi:</small>{{ $item }}<br>
                            <small>Price:</small>Rp. {{ number_format($ticket['schedule']['price']) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
