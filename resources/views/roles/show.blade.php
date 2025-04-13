<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
@include('layouts.adminNavbar')

<div class="container mt-5">

    <h1 class="mb-4">Users with the role: <span class="text-primary">{{ $role->name }}</span></h1>

    <form action="{{ route('roles.show', $role->id) }}" method="GET">
        <input type="text" name="search" placeholder="Search users" value="{{ request()->input('search') }}">
        <button type="submit">Search</button>
    </form>

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

                    @if(!$user->hasRole($role->name))
                        <form action="{{ route('roles.update', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Make {{ $role->name }}</button>
                        </form>

                    @elseif($user->hasRole($role->name))
                        <form action="{{ route('roles.updateRemove', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <button type="submit" class="btn btn-warning btn-sm">Remove {{ $role->name }}</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3">
        @if ($users->onFirstPage())
            <span class="btn btn-outline-secondary disabled">← Previous</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="btn btn-outline-primary">← Previous</a>
        @endif

        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="btn btn-outline-primary">Next →</a>
        @else
            <span class="btn btn-outline-secondary disabled">Next →</span>
        @endif
    </div>
</div>

</body>
</html>
