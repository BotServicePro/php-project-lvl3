@extends('layouts.app')


@section('title', 'Added urls')


@section('content')
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
            <td><a href="{{ route('show.url', ['id' => $url->id]) }}">{{ $url->name }}</a></td>
            <td>{{ $url->updated_at }}</td>
            <td>
                @isset($checksData[$url->id]->status_code)
                    {{  $checksData[$url->id]->status_code }}
                @endisset
            </td>
        </tr>
        @endforeach
    </table>
@endsection

