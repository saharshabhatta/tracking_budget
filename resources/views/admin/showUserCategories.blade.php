
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Categories</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
@include('layouts.adminNavbar')
<div class="container mt-4">
    <h1 class="text-center mb-4">Categories for {{ $user->first_name }}</h1>
    <div class="row">
        <div class="col-12">
            <h4>Selected Categories:</h4>
            <ul class="list-group">
                @foreach($user->categories as $category)
                    <li class="list-group-item">{{ $category->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
</body>

</html>
