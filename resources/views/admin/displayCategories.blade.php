<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmEdit(categoryId, userCount) {
            const confirmation = confirm(`This category is being used by ${userCount} users. Are you sure you want to edit it?`);
            if (confirmation) {
                window.location.href = `/admin/categories/${categoryId}/edit`;
            }
        }

        function confirmDelete(categoryId, userCount) {
            const confirmation = confirm(`This category is being used by ${userCount} users. Do you really want to delete it?`);
            if (confirmation) {
                document.getElementById(`delete-form-${categoryId}`).submit();
            }
        }
    </script>
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-5">
    <h1 class="text-center mb-5">All Categories</h1>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.categories') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="from" class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" id="from">
                    @error('from')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" id="to">
                    @error('to')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="filter-btn" class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" id="filter-btn" class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.categories') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" value="{{ $searchTerm }}" class="form-control" placeholder="Search categories...">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </div>
        </form>
    </div>

    <div class="text-end mb-3">
        <a href="{{ route('admin.createCategory') }}" class="btn btn-primary">+ Create New Category</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Number of Users</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->user_count }}</td>
                    <td>{{ $category->user_name }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning" onclick="confirmEdit({{ $category->id }}, {{ $category->user_count }})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $category->id }}, {{ $category->user_count }})">Delete</button>
                        </div>

                        <form id="delete-form-{{ $category->id }}" action="{{ route('admin.destroyCategory', $category->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @endforeach
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
</div>

</body>
</html>
