<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Monthly Spending Forecast</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center mb-4">Forecast</h3>
    <ul class="list-group">
        @foreach($forecast as $category => $amount)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $category }}</span>
                <span>{{ number_format($amount, 2) }}</span>
            </li>
        @endforeach
    </ul>
    <form action="{{ route('register.finalize') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">Finalize Registration</button>
    </form>
</div>
</body>
</html>
