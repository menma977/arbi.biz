@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Dashboard</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">Dashboard</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Quick Example</h3>
      </div>
      <form method="post" action="{{ route('notification.store') }}">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label for="_title">Title</label>
            <input type="text" class="form-control" id="_title" name="title" placeholder="Title">
          </div>
          <div class="form-group">
            <label for="_message">Message</label>
            <input type="text" class="form-control" id="_message" name="message" placeholder="Message">
          </div>
          <div class="form-group">
            <label for="_type">Select</label>
            <select class="form-control" id="_type" name="type">
              <option value="info">Info</option>
              <option value="warning">Warning</option>
              <option value="danger">Danger</option>
            </select>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection