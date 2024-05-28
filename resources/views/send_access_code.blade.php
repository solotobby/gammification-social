<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stylish Form</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
  </style>
</head>
<body>

<div class="container">
  <div class="form-container">
    <h2 class="form-title">Validation Form</h2>
    <form method="POST" action="{{ route('immaculate')}}">
        @csrf
      {{-- <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required>
      </div> --}}
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label for="email">Level</label>
        <select name="level" class="form-control" required>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}">{{$level->name}}</option>
            @endforeach
        </select>
        {{-- <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required> --}}
      </div>
      <div class="form-group">
        <label for="validationCode">Validation Code</label>
        <input type="text" class="form-control" name="validationCode" id="validationCode" placeholder="Enter validation code" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Submit</button>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
