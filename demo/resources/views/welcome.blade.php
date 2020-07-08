@extends('layout')

@section('content')

    <h1>
        Laravel Eloquent Imagery
    </h1>

    <div>

        <h2>Rendering Examples (Legacy URL Strategy)</h2>

        {{ $darkWidget->image->url() }}
        <br>
        <img src="{{ $darkWidget->image->url() }}">

        <br><br>

        {{ $darkWidget->image->url('grayscale') }}
        <br>
        <img src="{{ $darkWidget->image->url('grayscale') }}">

        <br><br>

        {{ $darkWidget->image->url('fit_scale|size_200x') }}
        <br>
        <img src="{{ $darkWidget->image->url('fit_scale|size_200x') }}">

        <br><br>

        {{ $darkWidget->image->url('fit_lpad|size_500x|bg_800080') }}
        <br>
        <img src="{{ $darkWidget->image->url('fit_lpad|size_500x|bg_800080') }}">

        <br><br>

        {{ $darkWidget->image->url('fit_resize|size_100x50') }}
        <br>
        <img src="{{ $darkWidget->image->url('fit_resize|size_100x50') }}">

        <br><br>

        {{ $darkWidget->image->url('fit_limit|size_300x200') }}
        <br>
        <img src="{{ $darkWidget->image->url('fit_limit|size_300x200') }}">

    </div>


@endsection
