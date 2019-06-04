@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Enter message</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="#" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea class="form-control" name="message" id="message" cols="30" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="radio-inline">
                                <input type="radio" name="encrypt_method" value="md5" checked> md5
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="encrypt_method" value="sha3-512"> sha3-512
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="encrypt_method" value="sha256"> sha256
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="encrypt_method" value="gost"> gost
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success float-right" id="crypt">Зашифровать</button>
                    </form>
                </div>
            </div>
            <br>
            <div id="message-block">
                @foreach($messages as $message)
                    @include('message')
                @endforeach
            </div>
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
