<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Categories</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.navbar')
<div class="container mt-5">
    <h2 class="text-center mb-4">Select Categories</h2>
    <form action="{{ route('register.store-categories') }}" method="POST">
        @csrf
        <div class="form-group">
            <h4>Select Categories:</h4>
            @foreach($categories as $category)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="categories[]" value="{{ $category->id }}" id="category-{{ $category->id }}">
                    <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
        <div class="form-group">
            <h4>Add a New Category (Optional):</h4>
            <input type="text" class="form-control" name="new_category" placeholder="Category Name">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Next</button>
    </form>
</div>

</body>
</html>
