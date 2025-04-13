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

        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @elseif(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3">
            <div class="alert alert-info">
                You have used <strong>{{ $totalSpent }}%</strong> out of 100% .
            </div>
        </div>

        <form method="POST" action="{{ route('categories.update', $category->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name', $category->name) }}" required>
                @error('name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="spending_percentage" class="form-label">Spending Percentage</label>
                <input type="number" id="spending_percentage" name="spending_percentage" class="form-control"
                       value="{{ old('spending_percentage', $user_category->spending_percentage ?? 0) }}" required
                       min="0" max="{{ 100 - $totalSpent }}">
                @error('spending_percentage')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-center">
                <button id="submitBtn" type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>

    <script>
        const spendingInput = document.getElementById('spending_percentage');
        const submitBtn = document.getElementById('submitBtn');
        const remaining = {{ 100 - $totalSpent }};

        spendingInput.addEventListener('input', function () {
            if (parseFloat(this.value) > remaining) {
                submitBtn.disabled = true;
                this.classList.add('is-invalid');
            } else {
                submitBtn.disabled = false;
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
