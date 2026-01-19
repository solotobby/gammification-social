@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css')}}">

@endsection

@section('content')


<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            Daily Engagement Analytics for {{ $user->name }}
          </h3>
        </div>

        <div class="block-content block-content-full">
          <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
          <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
            <thead>
              <tr>
                <th>Date</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Comments</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
                <?php
                    $totalViews = 0;
                    $totalLikes = 0;
                    $totalComments = 0;
                    $totalPoints = 0;
                ?>
                @foreach ($dailyEngagements as $engagement)
                <tr>
                    <td>
                     {{ $engagement->date }}
                    </td>
                    <td>
                        {{ $engagement->views }}
                    </td>
                    <td>
                        {{ $engagement->likes }}
                    </td>
                    <td >
                      {{ $engagement->comments }}
                    </td>
                    <td>
                        {{ $engagement->points }}
                      </td>
                  </tr>
                  <?php
                      $totalViews += $engagement->views;
                      $totalLikes += $engagement->likes;
                      $totalComments += $engagement->comments;
                      $totalPoints += $engagement->points;
                  ?>
                @endforeach
            </tbody>
          </table>

          <ul class="list-group mt-3">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Total Views
              <span class="badge bg-primary rounded-pill">{{ $totalViews }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Total Likes
              <span class="badge bg-primary rounded-pill">{{ $totalLikes }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Total Comments
              <span class="badge bg-primary rounded-pill">{{ $totalComments }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Total Engagement Points
              <span class="badge bg-primary rounded-pill">{{ $totalPoints }}</span>
            </li>
        </ul>

          <a href="{{ url('user/info/' . $user->id) }}" class="btn btn-secondary mt-3"> Back to User Info </a>

    </div>
</div>


@endsection