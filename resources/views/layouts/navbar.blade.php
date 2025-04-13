<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{route('dashboard')}}">Dashboard</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('expenses.index') }}">Expenses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('incomes.index') }}">Income</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a>
                </li>
            </ul>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary me-2">Profile</a>

            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>
</body>
</html>
