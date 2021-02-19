@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Users</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">
          <a href="{{ route("dashboard.index") }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">Users</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
          <table class="table table-bordered text-center">
            <thead>
            <tr>
              <th style="width: 10px">#</th>
              <th>Name</th>
              <th>username</th>
              <th>password</th>
              <th>email</th>
              <th style="width: 100px">Bot_1</th>
              <th style="width: 100px">Bot_2</th>
              <th style="width: 200px">username_DOGE</th>
              <th style="width: 200px">password_DOGE</th>
              <th>Wallet</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $item)
              <tr>
                <td>{{ ($list->currentpage() - 1) * $list->perpage() + $loop->index + 1 }}.</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->username }}</td>
                <td>{{ $item->password_mirror }}</td>
                <td>{{ $item->email }}</td>
                <td>{!! $item->trade_fake !!}</td>
                <td>{!! $item->trade_real !!}</td>
                <td>{{ $item->coinAuth->username }}</td>
                <td>{{ $item->coinAuth->password }}</td>
                <td>{{ $item->coinAuth->wallet }}</td>
                <td>
                  <a href="{{ route("user.suspend", $item->id) }}">
                    @if($item->suspend)
                      <button type="button" class="btn btn-block btn-success btn-xs">Unsuspend</button>
                    @else
                      <button type="button" class="btn btn-block btn-danger btn-xs">Suspend</button>
                    @endif
                  </a>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer clearfix">
        <div class="pagination pagination-sm m-0 float-right">
          {{ $list->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection