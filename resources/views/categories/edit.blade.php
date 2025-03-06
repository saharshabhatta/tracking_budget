<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.navbar')
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Category</h2>
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" value="{{ $category->name }}" class="form-control" required>
        </div>

{{--        <div class="mb-3">--}}
{{--            <label for="spending_percentage" class="form-label">Spending Percentage</label>--}}
{{--            <input type="number" id="spending_percentage" name="spending_percentage" value="{{$user_categories->spending_percentage}}" class="form-control" required>--}}
{{--        </div>--}}

        <button type="submit" class="btn btn-success">Update Category</button>
    </form>
</div>

</body>
</html>
