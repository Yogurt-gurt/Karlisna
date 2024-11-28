<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Virtual Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create Virtual Account</h2>
        <p>Klik tombol di bawah ini untuk mendapatkan Access Token dan membuat Virtual Account.</p>
        <form action="{{ route('submit-and-create-va') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        @if (session('response'))
            <div class="mt-4">
                <h4>Response:</h4>
                <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
</body>
</html>
