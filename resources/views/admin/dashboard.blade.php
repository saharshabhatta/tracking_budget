<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    @include('layouts.adminNavbar')

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Admin Dashboard</h3>
            </div>
            <div class="card-body">
                <h4>Total Users</h4>
                <p class="h5">{{ $totalUsers }}</p>

                <h4>Average Salary</h4>
                <p class="h5">{{ $averageIncome }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Top 5 Categories by Spending</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($categoriesSpending as $category)
                        <li class="list-group-item">
                            {{ $category['category'] }}: {{ number_format($category['total_spent'], 2) }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top 5 Categories with the Most Users</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($categoriesWithMostUsers as $category)
                        <li class="list-group-item">
                            {{ $category->name }}: {{ $category->users_count }} users
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
