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

<div class="container mt-4">
    <h1 class="text-center mb-4">Categories</h1>

    <form method="GET" action="{{ route('admin.userCategories', $user->id) }}" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by category name" value="{{ request()->query('search') }}">
            </div>

            <div class="col-md-4">
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}">
            </div>

            <div class="col-md-3 d-grid">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-12">
            <h4>Selected Categories:</h4>
            @if($categories->isEmpty())
                <p>No categories found for this month.</p>
            @else
                <ul class="list-group mb-3">
                    @foreach ($categories as $category)
                        <li class="list-group-item">
                            <strong>{{ $category->name }}</strong>
                            <span class="text-muted float-end">{{ \Carbon\Carbon::parse($category->create_date)->format('d M Y') }}</span>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination Controls -->
                <div class="d-flex justify-content-between align-items-center">
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
</div>

</body>
</html>
