<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تغيير كلمة المرور</title>
</head>
<body style="font-family: sans-serif; padding: 24px;">

    <h2>تغيير كلمة المرور</h2>

    @if (session('status'))
        <div style="padding:10px; background:#e7ffe7; margin: 12px 0;">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div style="padding:10px; background:#ffe7e7; margin: 12px 0;">
            <ul style="margin:0; padding-right:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf

        <div style="margin-bottom:12px;">
            <label>كلمة المرور الحالية</label><br>
            <input type="password" name="current_password" style="width:320px; padding:8px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>كلمة المرور الجديدة</label><br>
            <input type="password" name="password" style="width:320px; padding:8px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>تأكيد كلمة المرور الجديدة</label><br>
            <input type="password" name="password_confirmation" style="width:320px; padding:8px;">
        </div>

        <button type="submit" style="padding:10px 16px;">حفظ</button>
        <a href="{{ route('profile.edit') }}" style="margin-right:12px;">رجوع</a>
    </form>

</body>
</html>
