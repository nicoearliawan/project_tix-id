@extends('Templates.app')

@section('content')
    <div class="container my-5">
        @if (Session::get('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Excel (.xlsx)</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mb-3">Data Promo</h5>
        <table class="table table-bordered" id="promosTable">
            <tr class="text-center">
                <th>No</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ($promos as $index => $item)
                <tr>
                    $index dari 0, biar muncul dri 1 -> +1
                    <th>{{ $index + 1 }}</th>
                    name dan location dari fillable model cinema
                    <th>{{ $item['promo_code'] }}</th>
                    <th>
                        @if ($item->type === 'rupiah')
                             Rp. {{ number_format($item['discount'], 0 , ',','.') }}
                        @else
                             {{ number_format($item['discount']) }}%
                        @endif
                    </th>


                    <th class="d-flex justify-content-center">
                        'id' => $item['id'] : mengirimkan $item['id'] ke route {id}
                        <a href="{{ route('staff.promos.edit', ['id' => $item['id']]) }}" class="btn btn-info me-2">Edit</a>
                        <form action="{{ route('staff.promos.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </th>
                </tr>
            @endforeach --}}
        </table>
    </div>
    </div>
@endsection

@push('script')

    <script>
            $(function () {
                $('#promosTable').DataTable({
                    processing: true,
                    // data untuk datatable diproses secara serverside (controller)
                    serverSide: true,
                    // routing menuju fungsi yang memproses data untuk datatable
                    ajax: '{{ route('staff.promos.datatables') }}',
                    columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable:false },
                    { data: 'promo_code', name: 'promo_code', orderable: true, searchable: true },
                    { data: 'type', name: 'type', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
                });
            });
    </script>

@endpush
