<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

<div class="container mt-5">
    <h3 class="text-center mb-4">Forecast</h3>
    <ul class="list-group">
        @foreach($user_categories as $index => $user_category)
            <li class="list-group-item d-flex justify-content-between">
                <span>Category: {{ $user_category->category->name }}</span>
                <span>Amount: {{ number_format($amount[$index], 2) }}</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-4 p-3 bg-white shadow-sm rounded text-center">
        <h5>Remaining Amount</h5>
        <p class="fw-bold text-success">{{ number_format($remainingAmount, 2) }}</p>
    </div>
</div>

</body>
</html>
