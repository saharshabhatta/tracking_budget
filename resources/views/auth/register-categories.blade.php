<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Categories</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
{{--@include('layouts.navbar')--}}
<div class="container mt-5">

    @if (session('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-center mb-4">Select Categories</h2>

    <form action="{{ route('register.store-categories') }}" method="POST">
        @csrf

        <div class="form-group">
            <h4>Select Categories:</h4>
            @foreach($categories as $category)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="categories[]" value="{{ $category->id }}" id="category-{{ $category->id }}"
                        {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                </div>
            @endforeach
            @error('categories')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <h4>Add a New Category (Optional):</h4>
            <input type="text" class="form-control" name="new_category" placeholder="Category Name" value="{{ old('new_category') }}">
            @error('new_category')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">Next</button>
    </form>
</div>

</body>
</html>
