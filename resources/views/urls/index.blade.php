@extends('layouts.app')


@section('title', 'Added urls')


@section('content')
    @isset($messages) {{-- если в переменной есть какое либо значение--}}
        @include('flash::message')
    @endisset
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Links</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
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
            <td>
                @isset($checksStatuses[$url->id]->status_code)
                    {{  $checksStatuses[$url->id]->status_code }}
                @endisset
            </td>
        </tr>
        @endforeach
    </table>
@endsection

