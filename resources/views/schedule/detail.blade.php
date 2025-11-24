@extends('Templates.app')

@section('content')
    <div class="container pt-5">
        <div class="w-75 d-block m-auto">
            <div class="d-flex">
                <div style="width:150px;height:200px">
                    <img src="{{ asset('storage/' . $movie['poster']) }}" alt="" class="w-100">
                </div>
                <div class="ms-5 mt-4">
                    <h5>{{ $movie['title'] }}</h5>
                    <table>
                        <tr>
                            <td><b class="text-secondary">Genre</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['genre'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Durasi</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['duration'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Sutradara</b></td>
                            <td class="px-3"></td>
                            <td>{{ $movie['director'] }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Rating Usia</b></td>
                            <td class="px-3"></td>
                            <td><span class="badge badge-warning">{{ $movie['age_rating'] }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="w-100 row mt-5">
                <div class="col-6 pe-5">
                    <div class="d-flex flex-column justify-content-end align-items-end">
                        <div class="d-flex">
                            <h3 class="text-warning me-2">9.5</h3>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <small>45996 Votes</small>
                    </div>
                </div>
                <div class="col-6 ps-5" style="border-left:2px solid #c7c7c7">
                    <div class="d-flex align-items-center">
                        <div class="fas fa-heart text-danger me-2"></div>
                        <b>Masukkan Watchlist</b>
                    </div>
                    <small>10.000 Orang</small>
                </div>
            </div>
            <div class="d-flex w-100 bg-light mt-3">
                <div class="dropdown">
                    <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                        Bioskop
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                        @foreach ($movie['schedules'] as $schedule)
                            <li><a class="dropdown-item" href="#">{{ $schedule['cinema']['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @php
                    // request()->get('name_query') : memangggil query params (?) di url
                    // jika ? nilainya ASC ubah jadi DESC
                    if (request()->get('sort_price') == 'ASC') {
                        $sortPrice = 'DESC';
                    } elseif (request()->get('sort_price') == 'DESC') {
                        // jika query params sort_price nilainya DESC ubah jadi ASC
                        $sortPrice = 'ASC';
                    } else {
                        // pertama kali klik sort, ASC
                        $sortPrice = 'ASC';
                    }

                    if (request()->get('sort_alphabet') == 'ASC') {
                        $sortAlphabet = 'DESC';
                    } elseif (request()->get('sort_alphabet') == 'DESC') {
                        // jika query params sort_alphabet nilainya DESC ubah jadi ASC
                        $sortAlphabet = 'ASC';
                    } else {
                        // pertama kali klik sort, ASC
                        $sortAlphabet = 'ASC';
                    }

                @endphp
                <div class="dropdown">
                    <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                        Sortir
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="?sort_alphabet={{ $sortAlphabet }}">Alphabet</a></li>
                        <li><a class="dropdown-item" href="?sort_price={{ $sortPrice }}">Harga</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-5">
                @foreach ($movie['schedules'] as $schedule)
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-between">
                            {{-- kanan --}}
                            <div>
                                <i class="fa-solid fa-building"></i>
                                <b class="ms-2">{{ $schedule['cinema']['name'] }}</b>
                                <br>
                                <small class="ms-3">{{ $schedule['cinema']['location'] }}</small>
                            </div>
                            {{-- kiri --}}
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>

                        </div>

                        <div class="d-flex gap-3 ps-3 my-2">
                            {{-- hours berbentuk array, sehingga gunakan loop untuk akses itemnya --}}
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{-- argumen pada fngsi selectedHour
                            1. $schedule->id : mengambil detail schedule yang akan dibeli
                            2. $index : mengambil index dari array hours untuk mengetahui jam berapa tiket akan dipesan
                            3. $ this : mengambil elemen html yang diklik secara penuh untuk diakses javascript --}}
                                <div class="btn btn-outline-secondary" style="cursor: pointer"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                    {{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
            <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
                <a href="javascript:void(0)" id="btnOrder"><i class="fa-solid fa-ticket me-2"></i>BELI TIKET</a>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        let selectedScheduleId = null;
        let selectedHourIndex = null;
        let lastClicked = null;

        function selectedHour(scheduleId, hourIndex, el) {
            selectedScheduleId = scheduleId;
            selectedHourIndex = hourIndex;

            if (lastClicked) {
                lastClicked.style.backgroundColor = "";
                lastClicked.style.color = "";
                lastClicked.style.borderColor = "";
            }

            el.style.backgroundColor = "#112646";
            el.style.color = "white";
            el.style.borderColor = "#112646";

            lastClicked = el;

            let wrapBtn = document.querySelector("#wrapBtn");
            // hapus class (classList.remove)
            wrapBtn.classList.remove("bg-light");
            wrapBtn.style.backgroundColor = '#112646';
            // memanggil route web.php di JS
            // .replace() mengganti/mengisi path dinamis {scheduleId} di web.php
            let url = "{{ route('schedules.show_seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
                .replace(':scheduleId', scheduleId).replace(':hourId', hourIndex);

            let btnOrder = document.querySelector("#btnOrder");
            btnOrder.href = url;
            btnOrder.style.color = 'white';
        }
    </script>
@endpush
