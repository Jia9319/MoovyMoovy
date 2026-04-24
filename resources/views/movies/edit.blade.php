@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mm-movie-edit.css') }}">
@endpush

@section('content')
<section style="padding:120px 5% 4rem; max-width:800px; margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <a href="{{ route('movies.show', $movie->id) }}" style="color:var(--muted); text-decoration:none; font-size:0.875rem;">
            <i class="fas fa-arrow-left"></i> Back to Movie
        </a>
        <h1 class="sec-title" style="margin-top:1rem;">Edit <span class="acc">Movie</span></h1>
    </div>

    {{-- React Renders the Form Directly Here --}}
    <div id="editMovie" data-movie="{{ json_encode($movie) }}"></div>
</section>

<script src="{{ asset('js/app.js') }}"></script>
@endsection