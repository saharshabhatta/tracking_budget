<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')

<div class="container py-5">
    <div class="mb-4 text-center">
        <h1 class="fw-bold text-primary">Admin Dashboard</h1>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people-fill text-primary me-2"></i>Total Users</h5>
                    <p class="display-6">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cash-stack text-success me-2"></i>Average Salary</h5>
                    <p class="display-6">{{ $averageIncome }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Top 5 Categories by Spending</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($categoriesSpending as $category)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $category['category'] }}
                        <span class="badge bg-secondary rounded-pill">{{ number_format($category['total_spent'], 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Top 5 Categories with the Most Users</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($categoriesWithMostUsers as $category)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $category->name }}
                        <span class="badge bg-success-subtle text-dark">{{ $category->users_count }} users</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
