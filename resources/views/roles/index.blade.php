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
    <h1 class="mb-4">Roles List</h1>

    <table class="table table-bordered">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Role Name</th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td><a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
