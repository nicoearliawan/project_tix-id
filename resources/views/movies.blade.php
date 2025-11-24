@extends('Templates.app')

@section('content')
    <div class="container my-5">
        <h5 class="mb-5">Seluruh Film Sedang Tayang</h5>
        {{-- kalau form untuk searching gunakan GET & action kosong mengacu agar tetap dihalaman ini dan memproses ke route ini --}}
        <form class="row mb-3" method="GET" action="">
            @csrf
            <div class="col-10">
                <input type="text" name="search_movie" placeholder="Cari Judul Film..." class="form-control">
            </div>
            <div class="col-2">
                <button class="btn btn-primary">Cari</button>
            </div>
        </form>


        <div class="container d-flex gap-3 mt-4 justify-content-center">
            @foreach ($movies as $key => $item)
                <div class="card" style="width: 18rem;">
                    <img src="{{ asset('storage/' . $item['poster']) }}" class="card-img-top" alt="Sunset Over the Sea"
                        style="height: 450px; object-fit: cover;" />
                    <div class="card-body bg-primary text-warning" style="padding: 0 !important; text-align: center;">
                        <p class="card-text" style="padding: 0 !important; text-align: center; font-weight: bold;"><a
                                href="{{ route('schedules.detail', $item['id']) }}" class="text-warning">BELI TIKET</a></p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
