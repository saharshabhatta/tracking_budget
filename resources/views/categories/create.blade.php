<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')

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

    <div class="mb-3">
        <div class="alert alert-info">
            You have used <strong>{{ $totalSpent }}%</strong> out of 100%.
        </div>
    </div>

    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="spending_percentage" class="form-label">Spending Percentage</label>
            <input type="number" id="spending_percentage" name="spending_percentage" class="form-control"
                   value="{{ old('spending_percentage') }}" required>
            @error('spending_percentage')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-center">
            <button id="submitBtn" type="submit" class="btn btn-primary">Add Category</button>
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
