@extends('layouts.app')


@section('title', 'Main page')


@section('content')
    <main class="flex-grow-1">
        <div class="jumbotron jumbotron-fluid bg-dark">
            <div class="container-lg">
                <div class="row">
                    <div class="col-12 col-md-10 col-lg-8 mx-auto text-white">
                        <h1 class="display-3">Web page analyze</h1>
                        <p class="lead">Check web pages for SEO</p>
                        <form action="/urls" method="post" class="d-flex justify-content-center">
                            @csrf
                            <label>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="text" size="70" name="url[name]" value="" class="form-control form-control-lg" placeholder="https://www.example.com">
                            </label>
                            <button type="submit" class="btn btn-lg btn-primary ml-3 px-5 text-uppercase">CHECK</button>
                     </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
