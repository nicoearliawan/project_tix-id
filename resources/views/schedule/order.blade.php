@extends('Templates.app')
@section('content')

<div class="container card my-5 p-4">
    <div class="card-body">
        <b class="text-center">Ringkasan Order</b>
        <div class="d-flex m-5">
            <img src="{{ asset('storage/' . $ticket['schedule']['movie']['poster']) }}" width="120" alt="">
            <div class="ms-3">
                <h5 class="text-secondary">{{ $ticket['schedule']['cinema']['name'] }}</h5>
                <h5>{{ $ticket['schedule']['movie']['title'] }}</h5>
                <table>
                    <tr>
                        <td>Genre</td>
                        <td>:</td>
                        <td>{{ $ticket['schedule']['movie']['genre'] }}</td>
                    </tr>
                    <tr>
                        <td>Sutradara</td>
                        <td>:</td>
                        <td>{{ $ticket['schedule']['movie']['director'] }}</td>
                    </tr>
                    <tr>
                        <td>Durasi</td>
                        <td>:</td>
                        <td>{{ $ticket['schedule']['movie']['duration'] }}</td>
                    </tr>
                    <tr>
                        <td>Usia Minimal</td>
                        <td>:</td>
                        <td><span class="badge badge-danger">{{ $ticket['schedule']['movie']['age_rating'] }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <b>Detail Pemesanan</b>
        <table>
            <tr>
                <td>{{ $ticket['quantity'] }} Tiket</td>
                <td style="margin: 0 15px"></td>
                {{-- implode : mengubah array menadi string, dipisahkan dengan tanda tertentu --}}
                <td><b>{{ implode(', ', $ticket['rows_of_seats']) }}</b></td>
            </tr>
            <tr>
                <td>Harga Tiket</td>
                <td style="margin: 0 15px"></td>
                {{-- implode : mengubah array menadi string, dipisahkan dengan tanda tertentu --}}
                <td><b>Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}<span class="text-secondary"> X{{ $ticket['quantity'] }}</span></b></td>
            </tr>
            <tr>
                <td>Biaya Langganan</td>
                <td style="margin: 0 15px"></td>
                {{-- implode : mengubah array menadi string, dipisahkan dengan tanda tertentu --}}
                <td><b>Rp. 4.000 <span class="text-secondary"> X{{ $ticket['quantity'] }}</span></b></td>
            </tr>
        </table>
        <hr>
        <b>Gunakan Promo</b>
        <input type="hidden" id="ticket_id" value="{{ $ticket['id'] }}">
        <select id="promo_id" class="form-select" onchange="selectPromo()">
            <option value="null" disabled hidden selected>Pilih Promo</option>
            @foreach ($promos as $promo)
            <option value="{{ $promo['id'] }}">{{ $promo['promo_code'] }} - {{ $promo['type'] == 'percent' ? $promo['discount'] . '%' : 'Rp. ' . number_format($promo['discount'], 0, ',', '.') }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="fixed-bottom w-100 p-2 text-center text-white" style="background: #112646" onclick="createQrcode()"><b>Bayar Sekarang</b></div>

@endsection

@push('script')

    <script>
        let promoId = null;
        function selectPromo() {
            promoId = $("#promo_id").val(),
            console.log(promoId)
        }

        function createQrcode() {
            let data = {
                ticket_id: $("#ticket_id").val(),
                promo_id: promoId,
                _token: "{{ csrf_token() }}",
            }
            $.ajax ({
                url: "{{ route('tickets.qrcode') }}",
                method: "POST",
                data: data,
                success: function(response) {
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/payment`;
                },
                error: function(message) {
                    alert('Gagal membuat qrcode pembayaran!');
                }
            })
        }
    </script>

@endpush
