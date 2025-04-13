<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')
<div class="container mt-4">

    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-center mb-4">Edit Category</h1>

    <form method="POST" action="{{ route('admin.updateCategory', $category->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Users Associated: {{ $userCount }}</label>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">Update Category</button>
        </div>
    </form>
</div>
</body>
</html>
