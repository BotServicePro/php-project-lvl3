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
                @foreach ($urls as $url)
                    <tr>
                        <td>{{ $url->id }}</td>
                        <td><a href="{{ route('urls.show', ['id' => $url->id]) }}">{{ $url->name }}</a></td>
                        <td>{{ $url->updated_at }}</td>
                    <td>
                    @isset($lastChecks[$url->id]->status_code)
                        {{  $lastChecks[$url->id]->status_code }}
                    @endisset
                    </td>
                    </tr>
                @endforeach
            </table>
            {{ $urls->links() }}
        </div>
    </div>
@endsection

