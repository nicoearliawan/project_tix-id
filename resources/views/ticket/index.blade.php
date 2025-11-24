@extends('Templates.app')

@section('content')

<div class="container my-4 p-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Tiket Aktif</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Tiket Non-Aktif</button>
  </li>

</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
    <div class="container mt-4">
        <h5>Data Tiket untuk hari ini & besok</h5>
        <div class="d-flex flex-wrap">
            @foreach ($ticketActive as $active)
                <div class="w-25 me-3" style="border: 1px solid #eaeaea; padding: 8px;">
                    <b>{{ $active['schedule']['cinema']['name'] }}</b>
                    <br>
                    <b>{{ $active['schedule']['movie']['title'] }}</b>
                    <br>
                    <p>Tanggal : {{ \Carbon\Carbon::parse($active['ticketPayment']['booked_date'])->format('d F, Y') }}</p>
                    <p>Waktu : {{ \Carbon\Carbon::parse($active['hour'])->format('H:i') }} WIB</p>
                    <p>Kursi : {{ implode(',', $active['rows_of_seats']) }} WIB</p>
                    @php
                        $price = $active['total_price'] + $active['tax'];
                    @endphp
                    <p>Total Pembayaran : {{ number_format($price, 0, ',', '.') }}</p>
                    <a href="{{ route('tickets.export_pdf' , $active['id']) }}" class="btn btn-primary mt-2">Unduh</a>
                </div>

            @endforeach
        </div>
    </div>
  </div>
  <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
    <div class="container mt-4">
        <h5>Data Tiket Terlewatkan</h5>
        <div class="d-flex flex-wrap">
            @foreach ($ticketNonActive as $nonActive)
            <div class="w-25 me-3">
                    <b>{{ $nonActive['schedule']['cinema']['name'] }}</b>
                    <br>
                    <b>{{ $nonActive['schedule']['movie']['title'] }}</b>
                    <br>
                    <p>Tanggal : <span class="text-danger"> {{ \Carbon\Carbon::parse($nonActive['ticketPayment']['booked_date'])->format('d F, Y') }}</span></p>
                    <p>Waktu : {{ \Carbon\Carbon::parse($nonActive['hour'])->format('H:i') }} WIB</p>
                    <p>Kursi : {{ implode(',', $nonActive['rows_of_seats']) }} WIB</p>
                    @php
                        $price = $nonActive['total_price'] + $nonActive['tax'];
                    @endphp
                    <p>Total Pembayaran : {{ number_format($price, 0, ',', '.') }}</p>
                    <a href="{{ route('tickets.export_pdf' , $nonActive['id']) }}" class="btn btn-primary mt-2">Unduh</a>
                </div>

            @endforeach
        </div>

    </div>
  </div>
</div>
</div>

@endsection
