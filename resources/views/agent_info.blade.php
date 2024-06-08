<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partner Information</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  
  <style>
    .form-container {
      max-width: 500px;
      margin: 50px auto;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      border-radius: 10px;
      background-color: #f7f7f7;
    }
    .form-title {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
    }
    td{
    word-break:break;
    
    }


    
  </style>

<style>
    .info-list i {
        margin-right: 10px;
    }
    .info-list .row {
        padding: 10px 0;
    }
    .btn-custom {
        margin: 5px;
    }
</style>
</head>
<body>

{{-- <div class="container">
  <div class="form-container">
    <h2 class="form-title">Search</h2>
    <form method="POST" action="{{ route('immaculate')}}">
        @csrf
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Submit</button>
    </form>
  </div>
</div> --}}


<div class="container mt-5">
    <div class="col-12" >
        <div class="card">
            <div class="card-header">
                <h2>User Information</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold">Name:</div>
                    <div class="col-md-8" id="user-name">{{$transaction->user->name}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold">Email:</div>
                    <div class="col-md-8" id="user-email">{{$transaction->user->email}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold">Level:</div>
                    <div class="col-md-8" id="user-address">{{$transaction->user->level->name}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 font-weight-bold">Username:</div>
                    <div class="col-md-8" id="user-phone">{{$transaction->user->username}}</div>
                </div>
            </div>
            <div class="card-footer text-right">
                {{-- <button class="btn btn-primary">Edit</button>
                <button class="btn btn-danger">Delete</button> --}}
            </div>
        </div>
    </div>
</div>


<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2><i class="fas fa-user"></i>Slot Purchase Information</h2>
        </div>
        <div class="card-body">
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


            <div class="info-list">
                <?php  
                    $string = $transaction->meta;

                    $data = json_decode($string, true);
                ?>
                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-phone"></i> Partner Display Name:
                    </div>
                    <div class="col-md-8" id="user-phone">{{@$transaction->user->partner->name}}</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-user"></i> Level Bought:
                    </div>
                    <div class="col-md-8" id="user-name">{{ htmlspecialchars($data['package']) }}</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-user"></i> Unit Price:
                    </div>
                    <div class="col-md-8" id="user-name">{{$transaction->currency == 'USD' ? '$' : 'NGN'}} {{ htmlspecialchars($data['unitprice']) }}</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-envelope"></i> Total Slot Purchased:
                    </div>
                    <div class="col-md-8" id="user-email">{{ htmlspecialchars($data['number_of_slot']) }}</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-map-marker-alt"></i> Amount Paid:
                    </div>
                    <div class="col-md-8" id="user-address">{{$transaction->currency == 'USD' ? '$' : 'NGN'}} {{ number_format(htmlspecialchars($data['amount_paid'])) }}</div>
                </div>
                

                <div class="row align-items-center">
                    <div class="col-md-4 font-weight-bold">
                        <i class="fas fa-map-marker-alt"></i> Status:
                    </div>
                    <div class="col-md-8" id="user-address">{{ucfirst($transaction->status)}}</div>
                </div>
                
            </div>

            <hr>
            <h5>Current Slot</h5>
            {{-- {{ $slot->id }} <br> --}}
            Begginner: {{ @$slot->beginner }} <br>
            Creator: {{ @$slot->creator }} <br>
            Influencer : {{ @$slot->influencer }}<br>
            
        </div>
        <div class="card-footer text-right ">
        <form action="{{ route('validate.slot') }}" method="POST">
            @csrf
            <input type="hidden" name="partner_id" value="{{ @$slot->id }}">
            <input type="hidden" name="package" value="{{ htmlspecialchars($data['package'])  }}">
            <input type="hidden" name="slot_number" value="{{ htmlspecialchars($data['number_of_slot']) }}">
            <input type="hidden" name="tx_ref" value="{{ $transaction->ref }}">
            @if($transaction->status == 'allocated')
            <button class="btn btn-success btn-custom" type="submit"><i class="fas fa-check"></i> Validate</button>
            @else
            <button class="btn btn-success btn-custom disabled" ><i class="fas fa-check"></i> Validated</button>
            @endif
        <button class="btn btn-danger btn-custom"><i class="fas fa-times"></i> Reject</button>    
    </form>
     
        </div>
    </div>
    <a href="{{ url('view/agent/transaction')}}" class="btn btn-secondary mt-5">Back to List</a>

   
</div>




<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
    $('#example').DataTable();
} );
</script>

</body>
</html>
