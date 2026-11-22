@extends('layouts.app')

@section('title', 'Курсы из JSON-файла')

@section('content')
    @include('partials.course-form', ['action' => route('courses.json.store'), 'button' => 'Записать в JSON'])
    @include('partials.legend')
    @include('partials.course-table', ['rows' => $courses])
@endsection
