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
    <div class="card card-danger">
      <div class="card-header">
        <h3 class="card-title">Quick Example</h3>
      </div>
      <div class="card-body">
        <canvas id="bot2chart" height="180" style="height: 180px;"></canvas>
      </div>
    </div>
  </div>
@endsection

@section('addJs')
  <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>

  <script>
    $(function () {
      chartCamel()
    });

    function chartCamel() {
      let data = {
        labels: @json($historyBot),
        datasets: [
          {
            label: 'Marti Angel',
            backgroundColor: 'transparent',
            borderColor: '#17a2b8',
            pointRadius: 3,
            pointHoverRadius: 2,
            pointColor: '#17a2b8',
            pointStrokeColor: '#17a2b8',
            pointHighlightFill: '#17a2b8',
            pointHighlightStroke: '#17a2b8',
            data: @json($historyBot)
          },
        ]
      }

      let option = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
      };

      let chart = $('#bot2chart').get(0).getContext('2d')
      let chartData = jQuery.extend(true, {}, data)

      new Chart(chart, {
        type: 'line',
        data: chartData,
        options: option
      })
    }
  </script>
@endsection