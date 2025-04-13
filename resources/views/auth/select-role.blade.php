<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center mb-4">Select a Role to Continue</h1>

    <div class="row justify-content-center">
        <form method="POST" action="{{ route('choose.role') }}" class="row row-cols-1 row-cols-md-2 g-4">
            @csrf
            @foreach ($roles as $role)
                <div class="col">
                    <button type="submit" name="role_id" value="{{ $role->id }}" class="btn w-100 p-0 border-0 text-start">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h4 class="card-title">{{ ucfirst($role->name) }}</h4>
                                <p class="card-text">Continue as {{ ucfirst($role->name) }}</p>
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
        </form>
    </div>
</div>
</body>
</html>
