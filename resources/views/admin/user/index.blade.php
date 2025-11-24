@extends('Templates.app')

@section('content')
    <div class="container mt-5">
    @if (Session::get('success'))
        <div class="alert alert-success">{{Session::get('success')}}</div>
    @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.trash') }}" class="btn btn-primary me-2">Data Sampah</a>
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.users.create')}}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Pengguna (Admin & Staff)</h5>
        <table class="table table-bordered" id="usersTable">
            <tr class="text-center">
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ( $users as $index => $item)
                <tr>
                    $index dari 0, biar muncul dri 1 -> +1
                    <th>{{$index + 1}}</th>
                    name dan location dari fillable model cinema
                    <th>{{$item['name']}}</th>
                    <th>{{$item['email']}}</th>
                    @if ($item['role'] == 'admin')
                    <th><span class="badge badge-primary">{{$item['role']}}</span></th>
                    @elseif($item['role'] == 'staff')
                    <th><span class="badge badge-success">{{$item['role']}}</span></th>
                    @else<th class="text-warning">{{$item['role']}}</th>
                    @endif

                    <th class="d-flex justify-content-center">
                        'id' => $item['id'] : mengirimkan $item['id'] ke route {id}
                        <a href="{{route('admin.users.edit', ['id' => $item['id']])}}" class="btn btn-info me-2">Edit</a>
                        <form action="{{ route('admin.users.delete', ['id' => $item['id']]) }}" method="POST">
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
            $('#usersTable').DataTable({
                processing: true,
                // data untuk datatable diproses secara serverside (controller)
                serverSide: true,
                // routing menuju fungsi yang memproses data untuk datatable
                ajax: '{{ route('admin.users.datatables') }}',
                // urutan coulmn (td), pastikan urutan sesuai th
                // data: 'nama' -> nama diambil dari rawColumns jika AddColumns, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable:false },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'role_badge', name: 'role_badge', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            })
        })

    </script>
@endpush
