@extends('layouts.admin')

@section('content')


<div class="content">
    <h2 class="content-heading">Partner -  {{ $partners->user->name }} </h2>
<div class="row">

    <div class="col-md-6 col-xl-4">
      <a class="block block-rounded block-link-pop" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-user text-primary"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="fs-3 fw-medium mb-0">
                {{ $slot->beginner }}
            </p>
            <p class="text-muted mb-0">
              Beginner 
            </p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-xl-4">
      <a class="block block-rounded block-link-pop" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-users text-primary"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="fs-3 fw-medium mb-0">
                {{ $slot->creator }}
            </p>
            <p class="text-muted mb-0">
              Creator 
            </p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-xl-4">
      <a class="block block-rounded block-link-pop" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-users text-primary"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="fs-3 fw-medium mb-0">
                {{ $slot->influencer }}
            </p>
            <p class="text-muted mb-0">
                Influencer 
            </p>
          </div>
        </div>
      </a>
    </div>
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


    @if($partners->country == 'Nigeria')
        <div class="col-md-6">
          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
              <div>
                <i class="fa fa-2x fa-file text-primary"></i>
              </div>
              <div class="ms-3 text-end">
                <p class="fs-3 fw-medium mb-0">
                    {{ @$partners->account_number }}
                </p>
                <p class="text-muted mb-0">
                  {{ @$partners->bank_name }}
                </p>
                <p class="text-muted mb-0">
                  {{ @$partners->account_name }}
                </p>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-6">
          <a class="block block-rounded block-link-pop" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
              <div>
                <i class="fa fa-2x fa-dollar text-primary"></i>
              </div>
              <div class="ms-3 text-end">
                <p class="fs-3 fw-medium mb-0">
                    ${{ @$partners->balance_dollar }}
                </p>
                <small>&#8358 {{ @$partners->balance_dollar }}</small>
                <p class="text-muted mb-0">
                  Wallet Balance
                </p>
              </div>
            </div>
          </a>
        </div>

        <a href="{{ url('generate/virtual/account/'.$partners->id) }}" class="btn btn-primary">Generate Virtual Account</a>
    @endif

</div>
</div>
@endsection