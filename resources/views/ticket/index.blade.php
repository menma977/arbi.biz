@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Ticket</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">
          <a href="{{ route("dashboard.index") }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">Ticket</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-body">
        <form class="mb-2" method="post" action="{{ route('ticket.store') }}">
          @csrf
          <label for="addTicket">Add Ticket</label>
          <div class="input-group">
            <select class="select2" id="addTicket" name="username" style="width: 40%">
              @foreach($users as $user)
                <option class="dropdown-item" value="{{ $user->id }}">{{ $user->username }}</option>
              @endforeach
            </select>
            <input type="text" class="form-control" id="addTicket" name="total" placeholder="Enter total" value="{{ old("total") }}">
            <div class="input-group-append">
              <button type="submit" class="btn btn-success">Send!</button>
            </div>
          </div>
        </form>
        <form method="post" action="{{ route('ticket.remove') }}">
          @csrf
          <label for="removeTicket">Remove Ticket</label>
          <div class="input-group">
            <select class="select2" id="removeTicket" name="username" style="width: 40%">
              @foreach($users as $user)
                <option class="dropdown-item" value="{{ $user->id }}">{{ $user->username }}</option>
              @endforeach
            </select>
            <input type="text" class="form-control" id="removeTicket" name="total" placeholder="Enter total" value="{{ old("total") }}">
            <div class="input-group-append">
              <button type="submit" class="btn btn-danger">Remove!</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Bordered Table</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table class="table table-bordered text-center">
          <thead>
          <tr>
            <th style="width: 10px">#</th>
            <th>User</th>
            <th>Description</th>
            <th style="width: 40px">ADD</th>
            <th style="width: 40px">REMOVE</th>
            <th>Date</th>
          </tr>
          </thead>
          <tbody>
          @foreach($list as $item)
            <tr>
              <td>{{ ($list->currentpage() - 1) * $list->perpage() + $loop->index + 1 }}.</td>
              <td>{{ $item->user->username }}</td>
              <td>{{ $item->description }}</td>
              <td>{{ $item->debit }}</td>
              <td>{{ $item->credit }}</td>
              <td>{{ $item->date }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer clearfix">
        <div class="pagination pagination-sm m-0 float-right">
          {{ $list->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection

@section('addCss')
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('addJs')
  <!-- Select2 -->
  <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

  <script>
    $(function () {
      $('.select2').select2();
    });
  </script>
@endsection