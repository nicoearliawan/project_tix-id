@extends('Templates.app')

@section('content')

    <div class="w-75 d-block mx-auto m-5 p-4 shadow">
        <div>
          <h5 class="text-center mb-3">Buat Data Staff</h5>
        <form method="POST" action="{{ route('staff.promos.store') }}">
        @csrf
            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" name="promo_code" id="promo_code" class="form-control @error('promo_code') is-invalid @enderror">
                @error('promo_code')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                    <option value="" selected disabled>Pilih</option>
                    <option value="percent">%</option>
                    <option value="rupiah">Rp</option>
                </select>
                @error('type')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Jumlah Potongan</label>
                <input type="numeric" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror"></input>
                @error('discount')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <button class="btn btn-primary w-100" type="submit">kirim</button>
        </form>
        </div>

    </div>

@endsection
