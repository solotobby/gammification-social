<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partner List</title>
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
  <h4 class="mb-3">User List</h4>
    <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Level</th>
                    {{-- <th>Phone</th> --}}
                    <th>Registered</th>
                    {{-- <th>Start date</th>
                    <th>Salary</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                       
                        <td>{{ @$user->level->name}}</td>
                        <td>{{ $user->created_at}}</td>
                        {{-- <td><a href="{{ url('activate/'.$partner->id) }}"> {{ $partner->status == true ? 'YES' : 'NO' }}</a></td> --}}
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Level</th>
                    {{-- <th>Phone</th> --}}
                    <th>Registered</th>
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
