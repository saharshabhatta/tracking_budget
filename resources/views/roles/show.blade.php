<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Users with the role: <span class="text-primary">{{ $role->name }}</span></h1>

    @if ($users->isEmpty())
        <div class="alert alert-warning" role="alert">
            No users found for this role.
        </div>
    @else
        <div class="list-group">
            @foreach ($users as $user)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $user->first_name }}</strong> ({{ $user->email }})
                    </div>

                    @if($user->hasRole('user'))
                        <form action="{{ route('roles.updateToAdmin', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary btn-sm">Make Admin</button>
                        </form>
                    @elseif($user->hasRole('admin'))
                        <form action="{{ route('roles.updateToUser', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning btn-sm">Make User</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
