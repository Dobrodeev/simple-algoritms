@extends('layouts.app')

@section('title', 'Курсы валют Binance')

@section('content')
    @if ($error)
        <div class="alert alert-warning">{{ $error }}</div>
    @else
        @include('partials.legend')
        @include('partials.course-table', ['rows' => collect($prices)->pluck('price', 'symbol')])
    @endif
@endsection
