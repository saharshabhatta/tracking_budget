<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')
<div class="container mt-4">
    <h1 class="text-center mb-4">All Categories</h1>

    <div class="mb-3 text-end">
        <a href="{{ route('admin.createCategory') }}" class="btn btn-primary">Create New Category</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Number of Users</th>
                <th>Created By</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->user_count }}</td>
                    <td>
                        {{ $category->user_name}}
                    </td>
                    <td>
                        <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit">Delete</button>

                        </form>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
</body>
</html>
