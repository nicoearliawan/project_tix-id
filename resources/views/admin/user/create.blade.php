@extends('Templates.app')

@section('content')

    <div class="w-75 d-block mx-auto m-5 p-4 shadow">
        <div>
          <h5 class="text-center mb-3">Buat Data Staff</h5>
        <form method="POST" action="{{route('admin.users.store')}}">
        @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"></input>
                @error('email')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"></input>
                @error('password')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <button class="btn btn-primary w-100" type="submit">kirim</button>
        </form>
        </div>

    </div>

@endsection
