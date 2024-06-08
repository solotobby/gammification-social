<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partner Payments</title>
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

<div class="container mt-5" style="margin-top:10px;">
    <h4>Payment made By Partners</h4>

    <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Display Name</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Validate</th>
                    {{-- <th>Status</th> --}}
                    {{-- <th>Start date</th>
                    <th>Salary</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $partner)
                    <tr>
                        <td>{{ $partner->ref }}</td>
                        <td>{{ @$partner->user->partner->name }}</td>
                        <td>{{ $partner->amount }}</td>
                        <td>{{ $partner->currency}}</td>
                        <td>{{ $partner->status}}</td>
                        <td><a href="{{ url('agent/validate/activate/transaction/'.$partner->ref) }}" class="btn btn-secondary"> Validate Payment </a></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                  <th>Ref</th>
                    <th>Display Name</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Validate</th>
                </tr>
            </tfoot>
        </table>
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
