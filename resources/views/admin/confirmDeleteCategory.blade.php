<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-4">
    <h1 class="text-center mb-4">Delete Category: {{ $category->name }}</h1>

    <div class="alert alert-warning">
        <strong>Warning!</strong> This category is being used by {{ $userCount }} {{ Str::plural('user', $userCount) }}. Are you sure you want to delete it?
    </div>

    <div class="text-center">
        <form action="{{ route('admin.deleteCategoryConfirmed', $category->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Yes, Delete Category</button>
            <a href="{{ route('admin.categories') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
