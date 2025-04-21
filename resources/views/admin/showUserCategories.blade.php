<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-5">
    <h1 class="text-center mb-5">Categories for {{ $user->first_name }} {{ $user->last_name }}</h1>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.userCategories', $user->id) }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Category" value="{{ request()->query('search') }}">
                </div>

                <div class="col-md-4">
                    <label for="month" class="form-label">Filter by Month</label>
                    <input type="month" name="month" id="month" class="form-control" value="{{ $selectedMonth }}">
                </div>

                <div class="col-md-3 d-grid">
                    <button class="btn btn-outline-primary" type="submit">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <h4 class="mb-4">Selected Categories</h4>

        @if($categories->isEmpty())
            <p class="text-muted">No categories found for this month.</p>
        @else
            <ul class="list-group mb-4">
                @foreach ($categories as $category)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ $category->name }}</strong>
                        <span class="text-muted">{{ \Carbon\Carbon::parse($category->create_date)->format('d M Y') }}</span>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination Controls -->
            <div class="d-flex justify-content-between">
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
        @endif
    </div>
</div>

</body>
</html>
