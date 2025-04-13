<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #6366f1;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#">Expense Tracker</a>
        <div class="d-flex">
            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="display-4 fw-bold">Take Control of Your Finances</h1>
        <p class="lead mb-4">Track your expenses, manage your budget, and gain insights into your spending—all in one place.</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary fw-semibold shadow-sm">Start Tracking Now</a>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-5 fw-bold">Why Choose Our Tracker?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="feature-icon mb-3">📊</div>
                        <h5 class="card-title fw-semibold">Visual Insights</h5>
                        <p class="card-text">See where your money goes with charts and graphs that help you stay on track.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="feature-icon mb-3">📅</div>
                        <h5 class="card-title fw-semibold">Monthly Forecast</h5>
                        <p class="card-text">Get accurate forecasts based on past spending and plan for the future easily.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="feature-icon mb-3">🔒</div>
                        <h5 class="card-title fw-semibold">Secure & Private</h5>
                        <p class="card-text">Your financial data is safe and protected with strong encryption protocols.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white text-center py-4 border-top mt-5">
    <div class="container">
        <p class="mb-0 text-muted">&copy; {{ date('Y') }} Expense Tracker. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
