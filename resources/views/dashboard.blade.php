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

    <div class="text-center mb-3">
        <h5>Months of the Year</h5>
        <ul class="list-inline">
            @foreach($months as $key => $month)
                <li class="list-inline-item px-2">
                    <a href="{{ route('dashboard', ['month' => $key + 1]) }}" class="text-decoration-none
                        {{ $key + 1 == $selectedMonth ? 'fw-bold text-primary' : '' }}">
                        {{ $month }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="text-center mb-4">
        <h4>Monthly Income: {{ number_format($userIncome->monthly_income, 2) }}</h4>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Category</th>
                <th>Amount</th>
                <th>Limit</th>
                <th>Actual Limit</th>
                <th>Actual Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($user_categories as $index => $user_category)
                <tr>
                    <td>{{ $user_category->category->name }}</td>
                    <td class="text-end">Rs.{{ number_format($amount[$index], 2) }}</td>
                    <td class="text-end">{{ number_format($limit_percentage[$index] ?? 0, 2) }}%</td>
                    <td class="text-end">{{ number_format($actualLimits[$user_category->category->id] ?? 0, 2) }}%</td>
                    <td class="text-end">Rs.{{ number_format($actualAmounts[$user_category->category->id] ?? 0, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-3 bg-white shadow-sm rounded text-center">
        <h5>Remaining Amount</h5>
        <p class="fw-bold text-success">{{ number_format($remainingAmount, 2) }}</p>
    </div>

</div>
</body>
</html>
