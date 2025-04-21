<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

<div class="container mt-4">
    <h1 class="text-center mb-4">All Categories</h1>

    <form method="GET" action="{{ route('categories.index') }}" class="mb-3 row g-2">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by category name" value="{{ request()->query('search') }}">
        </div>

        <div class="col-md-4">
            <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}">
            @error('month')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3 d-grid">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>

    <div class="mb-3 text-end">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Create New Category</a>
    </div>

    <h3 class="mt-4">{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</h3>

    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('categories.edit', $category->id) }}"
                       class="btn btn-warning btn-sm"
                       onclick="return confirm('Are you sure you want to edit this category?');">Edit</a>

                    <form action="{{ route('categories.destroy', $category->id) }}"
                          method="POST"
                          style="display:inline;"
                          onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center">No categories found for this month.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-between mt-4">
        @if ($categories->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $categories->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($categories->hasMorePages())
            <a href="{{ $categories->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>
</body>
</html>
