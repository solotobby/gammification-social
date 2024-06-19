@extends('layouts.admin')

@section('content')

<!-- Page Content -->
<div class="content">
    <!-- Groups -->
    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title"></h3>
      </div>
      <div class="block-content">
        <form method="POST" action="{{ route('immaculate')}}">
            @csrf

          <!-- Text -->
          <h2 class="content-heading pt-0">Send Access Code</h2>
          <div class="row">
            <div class="col-lg-4">
              <p class="text-muted">
                {{-- Prepend or Append Text next to your inputs, useful if you you would like to add extra info --}}
              </p>
            </div>
            <div class="col-lg-8 col-xl-5">
                @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
                @endif
              <div class="mb-4">
                <div class="input-group">
                  <span class="input-group-text">
                    Email
                  </span>
                  <input type="text" class="form-control" name="email" id="example-group1-input1" name="example-group1-input1">
                </div>
              </div>
              <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">
                      Level
                    </span>
                    <select name="level" class="form-control" required>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">{{$level->name}}</option>
                        @endforeach
                    </select>
                </div>
              </div>
              <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">
                      Validate
                    </span>
                    <input type="text" class="form-control" name="validationCode" id="validationCode" placeholder="Enter validation code" required>
                  </div>
              </div>

              
              <div class="mb-4">
                <button type="submit" class="btn btn-sm btn-primary">Send Code</button>
              </div>
              
            </div>
          </div>
          <!-- END Text -->

          
        </form>
      </div>
    </div>
    <!-- END Groups -->
  </div>
  <!-- END Page Content -->

@endsection
