@extends('Templates.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5">
        <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data">
            {{-- enctype="multipart/form-data" untuk mengambil
        file yang diupload serta utuh bukan hanya nama filenya --}}
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Judul Film</label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title')
                    is-invalid @enderror"
                        value="{{ $movie->title }}">
                    @error('duration')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="duration" class="form-label">Durasi Film</label>
                    <input type="time" name="duration" id="duration"
                        class="form-control @error('duration')
                        is-invalid @enderror"
                        value="{{ $movie->duration }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="genre" class="form-label">Genre Film</label>
                    <input type="text" name="genre" id="genre" placeholder="Romantis, Fantasi"
                        class="form-control @error('genre')
                        is-invalid @enderror"
                        value="{{ $movie->genre }}">
                    @error('genre')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- director --}}
                <div class="col-6">
                    <label for="director" class="form-label">Sutradara Film</label>
                    <input type="text" name="director" id="director"
                        class="form-control @error('director')
                        is-invalid @enderror"
                        value="{{ $movie->director }}">
                    @error('director')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                {{-- usia --}}
                <div class="col-6">
                    <label for="age_rating" class="form-label">Usia Minimal</label>
                    <input type="number" name="age_rating" id="age_rating"
                        class="form-control @error('age_rating')
                        is-invalid @enderror"
                        value="{{ $movie->age_rating }}">
                    @error('age_rating')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- poster --}}
                <div class="col-6">
                    <label for="poster" class="form-label">Poster Film</label>
                    <img src="{{ asset('storage/' . $movie->poster) }}" style="height: 150px; object-fit: cover;"
                        class="d-block mx-auto mb-3" alt="">
                    <input type="file" name="poster" id="poster"
                        class="form-control @error('poster')
                        is-invalid @enderror"
                        value="{{ $movie->poster }}">
                    @error('poster')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- description --}}
            <div class="mb-3">
                <label for="description" class="form-label">Sinopsis</label>
                <textarea name="description" id="description" rows="5"
                    class="form-control @error('description')
                    is-invalid @enderror">{{ $movie->description }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            {{-- submit --}}
            <button type="submit" class="btn btn-primary">Ubah Data</button>
        </form>
    </div>
@endsection
