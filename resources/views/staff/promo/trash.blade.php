@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">

           <a href="{{route('staff.promos.index')}}" class="btn btn-secondary">Kembali</a>
        </div>

        <h3 class="my-3">Data Sampah Promo</h3>
        @if (Session::get('success'))
            <div class="alert alert-success ">{{ Session::get('success') }}</div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Aksi</th>
            </tr>
            @foreach ($promoTrash as $key => $promo)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $promo['promo_code'] ?? '-' }}</td>
                    <td>
                        @if ($promo->type === 'rupiah')
                             Rp. {{ number_format($promo['discount'], 0 , ',','.') ?? '-'  }}
                        @else
                             {{ number_format($promo['discount']) ?? '-'  }}%
                        @endif
                    </td>

                    <td class="d-flex">
                        <form action="{{route('staff.promos.restore', $promo->id)}}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success ms-2">Kembalikan</button>
                        </form>
                        <form action="{{route('staff.promos.delete_permanent', $promo->id)}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ms-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    </div>
    @endsection
