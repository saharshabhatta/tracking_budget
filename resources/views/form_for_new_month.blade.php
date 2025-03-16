<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income and Category Percentages</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.navbar')
<div class="container mt-5">
    <h2 class="text-center mb-4">Income and Category Percentages for {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }}</h2>
    <form action="{{ route('register.store-incomes') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="monthly_income">Monthly Income:</label>
            <input type="number" class="form-control" name="monthly_income" value="{{ old('monthly_income', $previousUserIncome->monthly_income ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="annual_income">Annual Income:</label>
            <input type="number" class="form-control" name="annual_income" value="{{ old('annual_income', $previousUserIncome->annual_income ?? '') }}" required>
        </div>

        <h3>Define Percentages for Categories:</h3>
        @if($categories->isNotEmpty())
            @foreach($categories as $category)
                <div class="form-group">
                    <label for="category_percentage_{{ $category->id }}">{{ $category->name }}:</label>
                    <input type="number" class="form-control" name="category_percentages[{{ $category->id }}]" min="0" max="100"
                           value="{{ old('category_percentages.' . $category->id, $previousCategoryPercentages[$category->id] ?? '') }}" required>
                    @if(isset($previousCategoryPercentages[$category->id]))
                        <small class="form-text text-muted">Previous month actual limit: {{ number_format($previousCategoryPercentages[$category->id], 2) }}%</small>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-danger">No categories available. Please select categories first.</p>
        @endif

        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
    </form>
</div>
</body>
</html>
