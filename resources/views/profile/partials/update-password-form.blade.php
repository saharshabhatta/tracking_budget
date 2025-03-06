<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section class="container mt-5">
    <header>
        <h2 class="h4 text-dark">
            Update Password
        </h2>

        <p class="mt-1 text-muted">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <!-- Current Password Field -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            @if ($errors->updatePassword->get('current_password'))
                <div class="text-danger mt-2">
                    @foreach ($errors->updatePassword->get('current_password') as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- New Password Field -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">New Password</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->get('password'))
                <div class="text-danger mt-2">
                    @foreach ($errors->updatePassword->get('password') as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger mt-2">
                    @foreach ($errors->updatePassword->get('password_confirmation') as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Save Button and Success Message -->
        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">Save</button>

            @if (session('status') === 'password-updated')
                <p class="text-muted small" style="display: block;">
                    Saved.
                </p>
            @endif
        </div>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
