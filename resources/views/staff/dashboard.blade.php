@extends('Templates.app')

@section('content')

<div class="container mt-5">
    <h5>Dashboard Petugas</h5>
    @if (Session::get('success'))
        <div class="alert alert-success"><b>Selamat Datang, {{Auth::user()->name}}</b></div>
    @endif
</div>

@endsection
