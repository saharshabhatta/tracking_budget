<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income and Category Percentages</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
{{--@include('layouts.navbar')--}}

<div class="container mt-5">
    <h2 class="text-center mb-4">Income and Category Percentages</h2>
    <form action="{{ route('register.store-incomes') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="monthly_income">Monthly Income:</label>
            <input type="number" class="form-control" name="monthly_income" required>
        </div>
        <div class="form-group">
            <label for="annual_income">Annual Income:</label>
            <input type="number" class="form-control" name="annual_income" required>
        </div>

        <h3>Define Percentages for Categories:</h3>

        @if($categories->isNotEmpty())
            @foreach($categories as $index => $userCategory)
                <div class="form-group">
                    <label for="category_percentage_{{ $index }}">{{ $userCategory->category->name }}:</label>
                    <input type="number" class="form-control" name="category_percentages[{{ $userCategory->category_id }}]" min="0" max="100" required>
                </div>
            @endforeach
        @else
            <p class="text-danger">No categories available. Please select categories first.</p>
        @endif

        <button type="submit" class="btn btn-primary btn-block">Next</button>
    </form>
</div>
</body>
</html>
