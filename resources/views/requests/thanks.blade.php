<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request received · {{ $clinicName }}</title>
    <style>
        body { margin:0; padding:48px 20px; font-family: Arial, sans-serif; background:#f1f5f9; color:#1e293b; }
        main { max-width:720px; margin:auto; padding:32px; background:#fff; border-radius:16px; }
        h1 { color:#0f766e; }
        code { font-size:1.4em; background:#f1f5f9; padding:4px 10px; border-radius:6px; }
    </style>
</head>
<body>
<main>
    <h1>Request received</h1>
    <p>Your request code is <code>{{ $clientRequest->request_code }}</code>. Please keep it.</p>
    <p>Status: {{ $clientRequest->status }}. Staff will review requests in first-come-first-served order and contact you with your schedule.</p>
    <p><a href="{{ route('home') }}">Back to home</a></p>
</main>
</body>
</html>