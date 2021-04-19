@extends('layouts.app')


@section('title', 'Added urls')


@section('header')
    <header  class="flex-shrink-0">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Анализатор страниц</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " href="/">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/urls">Сайты</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
@endsection


@section('Main descripton')
    <div><h3>Added links</h3></div>
@endsection

@section('linksDataTable')
    <div class="table-responsive">
    <table class="table table-bordered table-hover text-nowrap" style="width:100%">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Last updated</th>
            <th>Responsee answer</th>
        </tr>
        @foreach ($urlsData as $url)
        <tr>
            <td>{{ $url->id }}</td>
            <td><a href="/url/{{ $url->id }}">{{ $url->name }}</a></td>
            <td>{{ $url->updated_at }}</td>
            <td>-</td>
        </tr>
        @endforeach
    </table>
@endsection

@isset($messages) {{-- если в переменной есть какое либо значение--}}
@section('messages')
    <p>@include('flash::message')</p>
@endsection
@endisset


