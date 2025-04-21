<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container mt-5">
    <h1 class="text-center mb-5">All Users</h1>

    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.users') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search" placeholder="Search Users">
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    {{-- <h4>Total User: {{ $totalUsers }}</h4> --}}

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <a href="{{ url('/admin/user/' . $user->id . '/categories') }}" class="btn btn-sm btn-info">
                                View Categories
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-4">
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
    </div>
</div>

</body>
</html>
