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

    <div class="row mb-4">
        <form method="GET" class="col-md-6 offset-md-3">
            <div class="row">
                <div class="col-md-8">
                    <input type="month" name="month" class="form-control" value="{{ sprintf('%04d-%02d', $selectedYear, $selectedMonth) }}">
                </div>
                <div class="col-md-4 d-grid">
                    <button class="btn btn-primary" type="submit">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="text-center mb-4">
        <h4>Monthly Income: Rs.{{ number_format($userIncome, 2) }}</h4>
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
            @if(count($user_categories) > 0)
                @foreach($user_categories as $index => $category)
                    <tr>
                        <td>{{ $category['category']['name'] }}</td>
                        <td class="text-end">
                            Rs.{{ number_format($amount[$index] ?? 0, 2) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($limit_percentage[$index] ?? 0, 2) }}%
                        </td>
                        <td class="text-end">
                            {{ number_format($actualLimits[$category['category']['id']] ?? 0, 2) }}%
                        </td>
                        <td class="text-end">
                            Rs.{{ number_format($actualAmounts[$category['category']['id']] ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center text-muted">No forecast for this month.</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-3 bg-white shadow-sm rounded text-center">
        <h5>Remaining Amount</h5>
        <p class="fw-bold text-success">Rs.{{ number_format($remainingAmount, 2) }}</p>
    </div>
</div>

</body>
</html>
