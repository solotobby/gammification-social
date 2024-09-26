@extends('layouts.admin')

@section('content')



<div class="content">
    <h2 class="content-heading"> <i>{{ $user->name }} - {{ $user->wallet->level }}</i></h2>
    <div class="row">
      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-list text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                ${{  $user->wallet->balance }}
              </p>
              <p class="text-muted mb-0">
                Main Balance <span><small>(Earnings from signup bonus and content monetization)</small></span>
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-users text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                ${{  $user->wallet->referral_balance }}
              </p>
              <p class="text-muted mb-0">
                Referral Balance <span><small>(Earnings from inviting friends on Payhankey)</small></span>
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-thumbs-up text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
                ${{ $user->wallet->promoter_balance }}
              </p>
              <p class="text-muted mb-0">
                Promotion Balance <span><small>(Earnings from promoting Payhankey)</small></span>
              </p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-6">
        <a class="block block-rounded block-link-pop" href="javascript:void(0)">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between">
            <div>
              <i class="fa fa-2x fa-comments text-primary"></i>
            </div>
            <div class="ms-3 text-end">
              <p class="fs-3 fw-medium mb-0">
               ${{ $withdrawals }}
              </p>
              <p class="text-muted mb-0">
                Total Withdrawal <span><small>(Earnings withdrawn from your wallet)</small></span>
              </p>
            </div>
          </div>
        </a>
      </div>
    </div>


    <!-- Groups -->
    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title">Credit Wallet</h3>
      </div>
      <div class="block-content">
        <form method="POST" action="{{ route('credit.wallet')}}">
            @csrf
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
                    $
                  </span>
                  <input type="text" class="form-control" name="amount" id="example-group1-input1" name="example-group1-input1" required>
                </div>
              </div>
              <input type="hidden" value="{{ $user->id}}" name="user_id">
              {{-- <div class="mb-4">
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
              </div> --}}
              {{-- <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">
                      Validate
                    </span>
                    <input type="text" class="form-control" name="validationCode" id="validationCode" placeholder="Enter validation code" required>
                  </div>
              </div> --}}

              
              <div class="mb-4">
                <button type="submit" class="btn btn-sm btn-primary">Securely Credit User</button>
              </div>
              
            </div>
          </div>
          <!-- END Text -->

          
        </form>
      </div>
    </div>
    <!-- END Groups -->

    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title">List of Posts</h3>
      </div>
      <div class="block-content">
        <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
          <thead>
            <tr>
              {{-- <th class="text-center" style="width: 80px;">#</th> --}}
              <th>Content</th>
              <th>View-Ext_View-paid-Amt</th>
              <th>Likes-Ext_Likes-paid-Amt</th>
              <th>Comm-Ext_Comm-paid-Amt</th>
              <th>When Posted</th>
            </tr>
          </thead>
          <tbody>
              @foreach ($posts as $post)
              <tr>
                  {{-- <td class="text-center">1</td> --}}
                  <td>
                    {{\Illuminate\Support\Str::words($post->content, 3, '...') }}
                    {{-- {{ $post->content }} --}}
                    {{-- <a href="{{ url('user/info/'.$post->id) }}">{{ $post->user->name }}</a> --}}
                  </td>
                  <td>
                    {{ $post->views }}-{{ $post->views_external }}-{{ $post->paidExternalViews() }}|<span class="badge bg-info">${{ $post->paidExternalViews()*$singleViewExternal }}</span>
                  </td>
                  <td>
                    {{ $post->likes }}/{{ $post->likes_external }}
                    {{-- <span class="badge bg-info">{{ $post->views }}</span> --}}
                  </td>
                  <td>
                    {{ $post->comments }}-{{ $post->comment_external == null ? '0' : $post->comment_external }}-{{$post->paidExternalComments()}}| <span class="badge bg-info">${{$post->paidExternalComments()*$perCommentAmount }}</span>                  
                  </td>
                  <td>
                    <em class="text-muted">{{$post->created_at?->shortAbsoluteDiffForHumans()}} ago</em>
                  </td>
                </tr>
              @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>
  <!-- END Page Content -->

@endsection