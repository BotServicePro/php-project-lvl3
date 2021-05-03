@extends('layouts.app')


@section('title', 'Single url')


@isset($messages) {{-- если в переменной есть какое либо значение--}}
@section('messages')
    @include('flash::message')
@endsection
@endisset


@section('link_data')
    <div class="container-lg">
    <h1 class="mt-5 mb-3">Site: {{ $urlData->name }}</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-nowrap">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Created at</th>
                <th>Last update</th>
            </tr>

            <tr>
                <td>{{ $urlData->id }}</td>
                <td>{{ $urlData->name }}</td>
                <td>{{ $urlData->created_at }}</td>
                <td>{{ $urlData->updated_at }}</td>
            </tr>
        </table>
    </div>
    </div>
@endsection


@section('checks_data')
    <div class="container-lg">
            <h2 class="mt-5 mb-3">Checks</h2>
            <form action="/url/{{ $urlData->id }}/checks" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="submit" class="btn btn-primary" value="Start check">
            </form>
                <br>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <th>ID</th>
                        <th>Answer code</th>
                        <th>H1</th>
                        <th>Keywords</th>
                        <th>Description</th>
                        <th>Checking time</th>
                    </tr>
                    @foreach ($checksData as $check)
                        <tr>
                        <td>{{ $check->id }}</td>
                        <td>{{ $check->status_code }}</td>
                        <td>{{ $check->h1 }}</td>
                        <td>{{ $check->keywords }}</td>
                        <td>{{ $check->description }}</td>
                        <td>{{ $check->updated_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
@endsection
