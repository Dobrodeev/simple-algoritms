@extends('layouts.app')

@section('title', 'Курсы из базы данных')

@section('content')
    @include('partials.course-form', ['action' => route('courses.store'), 'button' => 'Добавить в базу'])
    @include('partials.legend')
    @include('partials.course-table', ['rows' => $courses->pluck('price', 'symbol')])
@endsection
