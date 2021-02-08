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
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit Announcement</h3>
      </div>
      <form method="post" action="{{ route('notification.store') }}">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="_title">Title</label>
                <input type="text" class="form-control" id="_title" name="title" placeholder="Title" value="{{ old("title") ?: $title }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="_type">Type</label>
                <select class="form-control" id="_type" name="type">
                  <option value="info" {{ $type == "info" ? "selected": "" }}>Info</option>
                  <option value="warning" {{ $type == "warning" ? "selected": "" }}>Warning</option>
                  <option value="danger" {{ $type == "danger" ? "selected": "" }}>Danger</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="_message">Message</label>
            <textarea class="form-control" id="_message" name="message" placeholder="Message" rows="5">{{ old("message") ?: $description }}</textarea>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection