<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incomes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@include('layouts.navbar')
    <div class="container">
{{--        @if (session('errors'))--}}
{{--            <div class="alert alert-danger">--}}
{{--                <ul>--}}
{{--                    @foreach ($errors->all() as $error)--}}
{{--                        <p>{{ $error }}</p>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        @endif--}}

        <h1 class="my-4">Add Income</h1>

        <form action="{{ route('incomes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Choose Income Type:</label>
                <div>
                    <input type="radio" name="income_type" value="monthly" id="monthlyOption" checked>
                    <label for="monthlyOption">Monthly Income</label>

                    <input type="radio" name="income_type" value="annual" id="annualOption">
                    <label for="annualOption">Annual Income</label>
                </div>
            </div>

            <div class="form-group" id="monthlyIncomeGroup">
                <label for="monthly_income">Monthly Income:</label>
                <input type="number" class="form-control" name="monthly_income" value="{{ old('monthly_income') }}">
                @error('monthly_income')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="annualIncomeGroup" style="display:none;">
                <label for="annual_income">Annual Income:</label>
                <input type="number" class="form-control" name="annual_income" value="{{ old('annual_income') }}">
                @error('annual_income')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Save Income</button>
        </form>
    </div>
<script>
    const monthlyOption = document.getElementById('monthlyOption');
    const annualOption = document.getElementById('annualOption');
    const monthlyGroup = document.getElementById('monthlyIncomeGroup');
    const annualGroup = document.getElementById('annualIncomeGroup');

    function toggleIncomeFields() {
        if (monthlyOption.checked) {
            monthlyGroup.style.display = 'block';
            annualGroup.style.display = 'none';
        } else {
            monthlyGroup.style.display = 'none';
            annualGroup.style.display = 'block';
        }
    }

    monthlyOption.addEventListener('change', toggleIncomeFields);
    annualOption.addEventListener('change', toggleIncomeFields);

    window.onload = toggleIncomeFields;
</script>
</body>
</html>
