<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOKU Token Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>DOKU Token Request</h2>
        <form action="{{ route('doku.request') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Request Token</button>
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
