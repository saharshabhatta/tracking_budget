@php use Carbon\Carbon; @endphp

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

<div class="container mt-5">
    <h1 class="text-center mb-4">Expenses</h1>

    <form method="GET" action="{{ route('expenses.index') }}" class="mb-4 row g-3">
        <div class="col-md-3">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
            @error('from')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
            @error('to')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Expenses">
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-secondary">Apply Filter</button>
        </div>
    </form>

    <div class="mb-3 text-end">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add New Expense</a>
    </div>

    @php
        $grouped = $expenses->groupBy(fn($expense) => Carbon::parse($expense->date)->format('Y-m'));
    @endphp

    @forelse($grouped as $month => $monthlyExpenses)
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">{{ Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($monthlyExpenses as $expense)
                        <tr>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->category->name }}</td>
                            <td>{{ $expense->amount }}</td>
                            <td>{{ Carbon::parse($expense->date)->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm"
                                   onclick="return confirm('Are you sure you want to edit this category?');">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}"  method="POST" style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            No expenses found for the selected date range.
        </div>
    @endforelse

    <div class="d-flex justify-content-between mb-5">
        @if ($expenses->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $expenses->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($expenses->hasMorePages())
            <a href="{{ $expenses->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>
</body>
</html>
