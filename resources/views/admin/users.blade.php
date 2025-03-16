<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')
<div class="container mt-4">
    <h1 class="text-center mb-4">All Users</h1>
    <h4>Total User: {{ $totalUsers }}</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
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
                        <a href="{{ url('/admin/user/' . $user->id . '/categories') }}" class="btn btn-info">View Categories</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
