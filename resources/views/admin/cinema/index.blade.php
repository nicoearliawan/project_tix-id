    @extends('Templates.app')

    @section('content')
        <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
                <a href="{{ route('admin.cinemas.export')}}" class="btn btn-secondary me-2">Export (.xlsx)</a>
                <a href="{{ route('admin.cinemas.create')}}" class="btn btn-success">Tambah Data</a>
            </div>
            <h5 class="mt-3">Data Bioskop</h5>
            <table class="table table-bordered" id="cinemasTable">
                <tr>
                    <th>#</th>
                    <th>Nama Bioskop</th>
                    <th>Lokasi Bioskop</th>
                    <th>Aksi</th>
                </tr>
                {{-- @foreach ( $cinemas as $index => $item)
                    <tr>
                        $index dari 0, biar muncul dri 1 -> +1
                        <th>{{$index + 1}}</th>
                        name dan location dari fillable model cinema
                        <th>{{$item['name']}}</th>
                        <th>{{$item['location']}}</th>
                        <th class="d-flex">
                            'id' => $item['id'] : mengirimkan $item['id'] ke route {id}
                            <a href="{{route('admin.cinemas.edit', ['id' => $item['id']])}}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('admin.cinemas.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>

                        </th>
                    </tr>
                @endforeach --}}
            </table>
        </div>
    @endsection

    @push('script') 
        <script>
            $(function () {
                $('#cinemasTable').DataTable({
                    processing: true,
                    // data untuk datatable diproses secara serverside (controller)
                    serverSide: true,
                    // routing menuju fungsi yang memproses data untuk datatable
                    ajax: '{{ route('admin.cinemas.datatables') }}',
                    columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable:false },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'location', name: 'location', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
                });
            });
        </script>
    @endpush
