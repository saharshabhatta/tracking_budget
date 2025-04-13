@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incomes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

<div class="container mt-5">
    <h1 class="text-center mb-4">Incomes</h1>

    <form method="POST" action="{{ route('incomes.filter') }}" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="mb-3 text-end">
        <a href="{{ route('incomes.create') }}" class="btn btn-primary">Add Income</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
        <tr>
            <th>Monthly Income</th>
            <th>Annual Income</th>
            <th>Month</th>
            <th>Year</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($income as $inc)
            <tr>
                <td>{{ $inc->monthly_income }}</td>
                <td>{{ $inc->annual_income }}</td>
                <td>{{ Carbon::createFromFormat('m', $inc->month)->format('F') }}</td>
                <td>{{ $inc->year }}</td>
                <td>
                    <a href="{{ route('incomes.edit', $inc->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('incomes.destroy', $inc->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No income records found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        @if ($income->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $income->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($income->hasMorePages())
            <a href="{{ $income->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>
</body>
</html>
