<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 w-100" style="max-width: 400px;">
        <h3 class="text-center mb-4">Login</h3>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                @error('password')
                <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>

            <div class="d-flex justify-content-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-muted">Forgot your password?</a>
                @endif

                <button type="submit" class="btn btn-primary">Log in</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>Don't have an account?</p>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register Account</a>
        </div>
    </div>
</div>

</body>

</html>
