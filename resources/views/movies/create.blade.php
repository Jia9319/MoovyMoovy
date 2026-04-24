@extends('layouts.app')

@section('title', 'Add Movie - MoovyMoovy')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/moovy-add.css') }}">
@endpush

@section('content')
    <div id="addMovie"></div>
    <script src="{{ asset('js/app.js') }}"></script>
@endsection