@extends('Templates.app')
@section('content')
    <div class="w-75 d-block mx-auto my-5 py-4">
        <h5 class="text-center mb-3">Tambah Data Bioskop</h5>
        <form method="POST" action="{{route('admin.cinemas.store')}}">
        @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Bioskop</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Lokasi</label>
                <textarea name="location" id="location" rows="5" class="form-control @error('location') is-invalid @enderror"></textarea>
                @error('location')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <button class="btn btn-primary" type="submit">kirim</button>
        </form>
    </div>
@endsection
