<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payhankey users · {{ $levelLabel }}</title>
    <style>
        :root { color-scheme: light; }
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #0f172a;
            margin: 24px;
            font-size: 12px;
        }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .meta { color: #64748b; margin-bottom: 18px; }
        .toolbar { margin-bottom: 16px; display: flex; gap: 8px; }
        .toolbar button {
            border: 1px solid #cbd5e1;
            background: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 600;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th { background: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
        tr:nth-child(even) td { background: #fcfcfd; }
        @media print {
            .toolbar { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <h1>Payhankey users</h1>
    <p class="meta">
        Level filter: {{ $levelLabel }} ·
        {{ number_format($users->count()) }} user(s) ·
        Exported {{ $exportedAt->format('M j, Y g:i A') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Level</th>
                <th>Verified</th>
                <th>Channel</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->userLevel?->plan_name ?? 'Basic' }}</td>
                    <td>{{ $user->email_verified_at ? 'Verified' : 'Pending' }}</td>
                    <td>{{ $user->heard ?: '—' }}</td>
                    <td>{{ $user->created_at?->format('M j, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No users found for this filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 250);
        });
    </script>
</body>
</html>
