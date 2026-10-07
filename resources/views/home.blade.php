<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $clinicName }}</title>

    <style>
        body {
            margin: 0;
            padding: 48px 20px;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        main {
            max-width: 720px;
            margin: auto;
            padding: 32px;
            background: white;
            border-radius: 16px;
        }

        h1 { color: #0f766e; }

        p, li { line-height: 1.7; }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #0f766e;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ $clinicName }}</h1>
        <p>Free tooth-removal request and scheduling system.</p>

        <h2>How scheduling works</h2>

        <ol>
            <li>The client submits a request.</li>
            <li>Staff review requests in first-come-first-served order.</li>
            <li>Staff assign an available appointment schedule.</li>
        </ol>

        <p><a class="button" href="{{ route('requests.create') }}">Request an appointment</a></p>
        <p><a href="{{ route('login') }}">Staff login</a></p>
    </main>
</body>
</html>
