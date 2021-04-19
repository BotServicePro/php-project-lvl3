@extends('layouts.app')


@section('title', 'Single url')


@section('header')
    <h1>Web page analyzer</h1>
@endsection


@section('Link Data')
    <h2>Site: {{ $urlData->name }}</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-nowrap" style="width:100%">
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
@endsection


@isset($messages) {{-- если в переменной есть какое либо значение--}}
    @section('messages')
        <p>@include('flash::message')</p>
    @endsection
@endisset

