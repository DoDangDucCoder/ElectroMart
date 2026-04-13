<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Co so du lieu chua san sang</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            font-family: Arial, sans-serif;
            color: #e2e8f0;
        }

        .panel {
            width: min(100%, 720px);
            padding: 32px;
            border-radius: 24px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
        }

        .badge {
            display: inline-block;
            margin-bottom: 16px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(251, 191, 36, 0.16);
            color: #facc15;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.15;
        }

        p {
            margin: 0 0 16px;
            line-height: 1.7;
            color: #cbd5e1;
        }

        code {
            padding: 2px 6px;
            border-radius: 8px;
            background: rgba(148, 163, 184, 0.12);
            color: #f8fafc;
        }

        a {
            color: #7dd3fc;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <main class="panel">
        <span class="badge">503 Service Unavailable</span>
        <h1>Khong the ket noi co so du lieu</h1>
        <p>{{ $message }}</p>
        <p>Neu ban dang chay local, hay kiem tra dich vu MySQL va cac bien <code>DB_HOST</code>, <code>DB_PORT</code>, <code>DB_DATABASE</code> trong file <code>.env</code>.</p>
        <p><a href="{{ url('/admin') }}">Thu lai trang admin</a></p>
    </main>
</body>

</html>
