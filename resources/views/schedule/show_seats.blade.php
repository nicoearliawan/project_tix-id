@extends('Templates.app')

@section('content')

<div class="container card my-5 p-4" style="margin-bottom: 10% !important">
    <div class="card-body">
        <b>{{ $schedule['cinema']['name'] }}</b>
        {{-- mengambil tgl hari ini : now(), format('d F, Y') F nama bulan --}}
        <br>
        <b>{{ now()->format('d F, Y') }} - {{ $hour }}</b>
        <br>
        <div class="alert alert-secondary">
            <i class="fa-solid fa-info text-danger me-3"></i>Anak usia 2 tahun keatas wajib membeli tiket.
        </div>
        <div class="w-50 d-block mx-auto my-3">
            <div class="row">
                <div class="col-4 d-flex">
                    <div style="background: #112646; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Tersedia</span>
                </div>
                <div class="col-4 d-flex">
                    <div style="background: blue; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Dipilih</span>
                </div>
                <div class="col-4 d-flex">
                    <div style="background: #eaeaea; width: 20px; height: 20px;"></div>
                    <span class="ms-2">Kursi Terjual</span>
                </div>
            </div>
        </div>
        @php
        // membuat array dengan rentan tertentu : range()
            $rows = range('A', 'H');
            $cols = range(1, 18);
        @endphp
        {{-- looping A-H ke bawah --}}
        @foreach ($rows as  $row)
        {{-- bikin looping 1-18 ditiap huruf A-H ke samping (d-flex) --}}
            <div class="d-flex justify-content-center">
                @foreach ($cols as $col)
                    {{-- jika kursi no 7 kasi kotak kosong untuk jalan --}}
                    @if ($col == 7)
                        <div style="width: 50px"></div>
                    @endif
                    @php
                        $seat = $row . "-" . $col
                    @endphp
                    {{-- cek apakah di array $seatsFormat ada data kursi ini : in_array() --}}
                    @if (in_array($seat, $seatsFormat))
                    <div style="background: #eaeaea; color: black; width: 40px; height: 35px; margin: 5px; border-radius: 5px; text-align: center; padding-top: 3px;)">
                        <small><b>{{ $row }}-{{ $col }}</b></small>
                    </div>
                    @else
                    {{-- bikin style kotak no kursi --}}
                    {{-- this : untuk mengubah element html yg akan dikirimkan ke js dan digunakan --}}
                    <div style="background: #112646; color: white; width: 40px; height: 35px; margin: 5px; border-radius: 5px; text-align: center; padding-top: 3px; cursor: pointer;" onclick="selectSeat('{{ $schedule->price }}', '{{$row}}', '{{$col}}', this)">
                        <small><b>{{ $row }}-{{ $col }}</b></small>
                    </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<div class="fixed-bottom">
    <div class="w-100 bg-light text-center px-3" style="border: 1px solid black"><b>LAYAR BIOSKOP</b></div>
    <div class="row bg-light">
        <div class="col-6 text-center p-3" style="border: 1px solid black">
            <b>Total Harga</b>
            <br><b id="totalPrice">Rp. -</b>
        </div>
        <div class="col-6 text-center p-3" style="border: 1px solid black">
            <b>Kursi Dipilih</b>
            <br><b id="selectedSeat">-</b>
        </div>
    </div>
    {{-- input :hidden menyembunyikan konten html, digunakan hanya untuk menyimpan nilai php untuk digunakan di JS --}}
    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
    <input type="hidden" name="hour" id="hour" value="{{ $hour }}">

    <div class="w-100 bg-light text-center py-3" style="font-weight: bold" id="btnCreateOrder">RINGKASAN ORDER</div>
</div>
@endsection

@push('script')

<script>
    let seats = [] // menyimpan data kursi yang sudah dipilih, bisa lebih dari 1
    // biar bisa di gunakan dalam function yang beda
    let totalPrice = 0;

    function selectSeat(price,row,col,element) {
        // buat format A-1
        let seat = row + "-" + col;
        // indexOf() cek isi array dan ambil indexnya
        let indexSeat = seats.indexOf(seat);
        // jika ada dapet indexnya, jika tidak ada -1
        if (indexSeat == -1) {
            // kalau item gaada di dalam array, tambhakan item tsb ke array
            seats.push(seat);
            // kasi warna biru terang
            element.style.background = 'blue';
        } else {
            // jika ada, maka klik kali ini untuk menghapus kursi (batal pilih)
            seats.splice(indexSeat, 1);
            element.style.background = '#112646';
        }

        totalPrice = price * seats.length; // length menghitung jumlah atau count isi array
        let totalPriceElement = document.querySelector("#totalPrice");
        totalPriceElement.innerText = "Rp. " + totalPrice;

        let selectedSeatElement = document.querySelector("#selectedSeat");
        // mengubah array jadi string dipisahkan dengan koma : join()
        selectedSeatElement.innerText = seats.join(', ');



        let btnCreateOrder = document.querySelector("#btnCreateOrder");
        if (seats.length > 0) {
            btnCreateOrder.style.background = '#112646';
            btnCreateOrder.style.color = 'white';
            btnCreateOrder.classList.remove("bg-light");
            // fungsi untuk memangggil ajax, dijalankan ketika btn di klik
            btnCreateOrder.onclick = createOrder;
        } else {
            btnCreateOrder.style.background = '';
            btnCreateOrder.style.color = '';
            btnCreateOrder.classList.onclick = null;
        }
    }

    function createOrder()
    {
        let data = {
            // sebelum titik dua diambil dari fillable dan sesudah titik dua diambil dari var js
            user_id: $("#user_id").val(), //ambil value dari input hidden id="user_id"
            schedule_id: $("#schedule_id").val(),
            rows_of_seats: seats,
            quantity: seats.length,
            total_price: totalPrice,
            tax: 4000 * seats.length,
            hour: $("#hour").val(),
            _token: "{{ csrf_token() }}", //token csrf
        }
        // ajax (asynchronus javascript and XML)  : memproses data ke atau dari BE
        $.ajax({
            url: "{{ route('tickets.store') }}", //route menuju proses data
            method: "POST", //http method
            data: data, //data yang akan dikirim ke BE
            success: function(response) {
                // kalo berhasil mau ngapain
                let ticketId = response.data.id;
                // pindah halaman : window.location.href
                // di js, jika ingin pindah halaman dengan route method nya
                window.location.href = `/tickets/${ticketId}/order`;
            },
            error: function(message) {
                alert('Gagal membuat data tiket!');
            }
        })
    }


</script>

@endpush
