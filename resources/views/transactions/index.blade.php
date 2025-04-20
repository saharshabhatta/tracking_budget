@php use Carbon\Carbon; @endphp

    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
@include('layouts.navbar')

<div class="container mt-5">
    <h2 class="text-center mb-4">Transactions</h2>
    <form method="GET" action="{{ route('transactions.index') }}" class="mb-4">
        <div class="row">
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
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </div>
    </form>
    <div class="row">
        @forelse($transactions as $transaction)
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        @if($transaction->type == 'income')
                            <h5 class="card-title text-success">Income</h5>
                            <a href="{{ route('incomes.edit', $transaction->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('incomes.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            @else
                            <h5 class="card-title text-danger">Expense (Category: {{ $transaction->category->name ?? 'N/A' }})</h5>
                            <a href="{{ route('expenses.edit', $transaction->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('expenses.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Amount: </strong>
                            @if($transaction->type == 'income')
                                {{ $transaction->monthly_income }}
                            @else
                                {{ $transaction->amount }}
                            @endif
                        </p>
                        <p class="card-text">
                            <strong>Date: </strong>
                            @if($transaction->type == 'income')
                                {{ Carbon::parse($transaction->created_date)->format('d-m-Y') }}
                            @else
                                {{ Carbon::parse($transaction->date)->format('d-m-Y') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">No transactions found.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-between my-4">
        @if ($transactions->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($transactions->hasMorePages())
            <a href="{{ $transactions->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>
</body>
</html>
