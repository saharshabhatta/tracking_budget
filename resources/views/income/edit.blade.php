<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incomes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

<div class="container mt-5">
    <h1 class="text-center mb-4">Edit Incomes</h1>
    <div class="container">
        <h1 class="my-4">Edit Income</h1>

        <form action="{{ route('incomes.update', $income->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="monthly_income">Monthly Income</label>
                <input type="number" name="monthly_income" id="monthly_income" class="form-control" value="{{ $income->monthly_income }}" required>
            </div>

            <div class="form-group">
                <label for="yearly_income">Annual Income</label>
                <input type="number" name="annual_income" id="annual_income" class="form-control" value="{{ $income->annual_income }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Income</button>
        </form>
    </div>
</div>
</body>
</html>


