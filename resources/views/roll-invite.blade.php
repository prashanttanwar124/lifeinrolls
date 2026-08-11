<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join {{ $rollTitle }} — Life in Rolls</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #141416; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #1b1b1d; border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 32px; max-width: 360px; text-align: center; }
        .code { font-size: 28px; letter-spacing: 4px; font-weight: 700; color: #e08a63; background: rgba(152,77,52,.15); border-radius: 12px; padding: 12px 16px; margin: 16px 0; }
        p { color: rgba(255,255,255,.6); font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>You're invited to “{{ $rollTitle }}”</h1>
        <p>Open the Life in Rolls app, choose <strong>Join Roll</strong>, and enter this code:</p>
        <div class="code">{{ $inviteCode }}</div>
        <p>Anyone with this code can join the roll.</p>
    </div>
</body>
</html>
