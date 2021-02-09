@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image" style="opacity: .8; width: 15%">
      <a href="{{ route('welcome') }}"><b>Arbi</b><small>.biz</small></a>
    </div>

    <div class="card elevation-3">
      <div class="card-body">
        <p><b>Hello</b>, {Email}</p>
        <label>Your Code is</label>
        <div class="input-group mb-3">
          <div class="form-control">
            CODE
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

