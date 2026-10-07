<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Staff') · {{ config('clinic.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f1f5f9; color: #1e293b; font-family: Arial, sans-serif; }
        header { padding: 18px 24px; background: #0f766e; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        header a { color: white; }
        main { max-width: 1100px; margin: 32px auto; padding: 24px; background: white; border-radius: 12px; }
        h1 { color: #0f766e; }
        label { display: block; margin-top: 16px; font-weight: bold; }
        input { display: block; width: 100%; padding: 12px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 6px; }
        button { padding: 10px 18px; border: 0; border-radius: 6px; background: #0f766e; color: white; cursor: pointer; font: inherit; }
        header button { background: white; color: #0f766e; }
        .login-form { max-width: 420px; margin: auto; }
        .submit-button { margin-top: 20px; }
        .error { color: #b91c1c; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        .pagination { display: flex; flex-wrap: wrap; gap: 20px; align-items: center; margin-top: 20px; }
        @media (max-width: 700px) { main { margin: 16px; padding: 16px; } }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}">{{ config('clinic.name') }}</a>
        @auth
            <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        @endauth
    </header>
    <main>@yield('content')</main>
</body>
</html>
