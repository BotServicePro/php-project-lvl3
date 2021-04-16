@extends('layouts.app')


@section('title', 'Single url')


@section('header')
    <h1>Web page analyzer</h1>
@endsection


@section('Main descripton')
    <div style="text-align: center;"><h1>Web page analyzer</h1></div>
    <div style="text-align: center;">Check sites for SEO for free</div>
@endsection

@isset($messages) {{-- если в переменной есть какое либо значение--}}
@section('messages')
    <p>@include('flash::message')</p>
@endsection
@endisset



@section('url_input_form')
    <form action="/postUrl" method="post">
        @csrf
        <label>
            <input type="text" name="url[name]" value="{{-- {{ $url['name'] }} --}}">
        </label>
        {{--        <?php if (isset($errors['description'])): ?>--}}
        {{--        <div><b><?= $errors['description'] ?></b></div>--}}
        {{--        <?php endif ?>--}}
        <input type="submit" value="CHECK">
    </form>
@endsection
