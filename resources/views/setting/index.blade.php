@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Announcement</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">
          <a href="{{ route("dashboard.index") }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">Announcement</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card card-outline card-danger">
      <div class="card-body">
        <div class="btn-group btn-block mb-3">
          <a href="{{ route("setting.maintenance", 0) }}" class="btn btn-outline-success {{ $setting->maintenance ? '' : 'active' }}">Open Server</a>
          <a href="{{ route("setting.maintenance", 1) }}" class="btn btn-outline-danger {{ $setting->maintenance ? 'active' : '' }}">Maintenance</a>
        </div>
        <form method="post" action="{{ route("setting.version") }}" class="mb-3">
          @csrf
          <label for="_version">Edit Version</label>
          <div class="input-group">
            <input type="number" class="form-control" name="version" id="_version" value="{{ old('version') ?? $setting->version }}">
            <div class="input-group-append">
              <button type="button" class="btn btn-info">Edit</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card card-outline card-danger">
      <div class="card-header">
        <h3 class="card-title">Bot Setting</h3>
      </div>
      <form method="post" action="{{ route('setting.bot') }}">
        @csrf
        <div class="card-body">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label for="_min" class="input-group-text">MIN</label>
            </div>
            <input type="text" class="form-control" name="min" id="_min" value="{{ old('min') ?? number_format($setting->min_bot / 10 ** 8, 8, '.', '') }}">
            <div class="input-group-prepend">
              <label for="_min" class="input-group-text">DOGE</label>
            </div>
            <div class="input-group-prepend">
              <label for="_max" class="input-group-text">MAX</label>
            </div>
            <input type="text" class="form-control" name="max" id="_max" value="{{ old('max') ?? number_format($setting->max_bot / 10 ** 8, 8, '.', '') }}">
            <div class="input-group-prepend">
              <label for="_max" class="input-group-text">DOGE</label>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-append">
                  <label for="_it" class="input-group-text">IT</label>
                </div>
                <input type="number" class="form-control" name="it" id="_it" value="{{ old('it') ?? $setting->it * 100 }}">
                <div class="input-group-append">
                  <label for="_it" class="input-group-text">%</label>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-append">
                  <label for="_buy_wall" class="input-group-text">Buy Wall</label>
                </div>
                <input type="number" class="form-control" name="buy_wall" id="_buy_wall" value="{{ old('it') ?? $setting->buy_wall * 100 }}">
                <div class="input-group-append">
                  <label for="_buy_wall" class="input-group-text">%</label>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-append">
                  <label for="_sponsor" class="input-group-text">Sponsor</label>
                </div>
                <input type="number" class="form-control" name="sponsor" id="_sponsor" value="{{ old('it') ?? $setting->sponsor * 100 }}">
                <div class="input-group-append">
                  <label for="_sponsor" class="input-group-text">%</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Edit</button>
        </div>
      </form>
    </div>
  </div>
@endsection