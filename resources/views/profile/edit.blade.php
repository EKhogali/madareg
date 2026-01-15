<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تعديل البيانات</title>
</head>
<body style="font-family: sans-serif; padding: 24px;">

    <h2>تعديل بياناتي</h2>

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

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom:12px;">
            <label>الاسم</label><br>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:320px; padding:8px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>الهاتف</label><br>
            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" style="width:320px; padding:8px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>الصورة</label><br>
            <input type="file" name="image">
            @if (!empty($user->image))
                <div style="margin-top:10px;">
                    <small>الصورة الحالية:</small><br>
                    <img src="{{ asset('storage/'.$user->image) }}" style="max-width:140px; border:1px solid #ddd;">
                </div>
            @endif
        </div>

        <button type="submit" style="padding:10px 16px;">حفظ</button>

        <a href="{{ route('profile.password.edit') }}" style="margin-right:12px;">
            تغيير كلمة المرور
        </a>
    </form>

</body>
</html>
