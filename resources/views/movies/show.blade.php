@extends('layouts.app')

@section('title', $movie->title . ' - MoovyMoovy')

@section('content')
    <div id="movieDetail" 
         data-movie-id="{{ $movie->id }}"
         data-user-id="{{ auth()->check() ? auth()->id() : 'null' }}"
         data-is-admin="{{ auth()->check() && auth()->user()->role === 'admin' ? 'true' : 'false' }}">
    </div>
@endsection

@push('scripts')
    <script>
        window.Laravel = {
            userId: {{ auth()->check() ? auth()->id() : 'null' }},
            isAdmin: {{ auth()->check() && auth()->user()->role === 'admin' ? 'true' : 'false' }}
        };
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
@endpush