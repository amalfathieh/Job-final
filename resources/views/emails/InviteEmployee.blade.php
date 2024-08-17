{{-- email.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<body>
<div class="container">
    <div class="content">
        <h1>Hello,</h1>
        <p>You have been invited by the admin to be an member on the management dashboard of the Job and Freelancer application.</p>
        <p>Please follow the link below to access your account:</p>
        <p><a href="{{ $link }}" target="_blank">{{ $link }}</a></p>
        <p>This will be your password: <strong>{{ $password }}</strong></p>
        <p>We recommend changing this password immediately after your first login for security reasons.</p>
    </div>
</div>
</body>
</html>

{{-- email.blade.php --}}
{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<body>--}}
{{--<div class="container">--}}
{{--    <div class="content">--}}
{{--        <h1>Hello,</h1>--}}
{{--        <p>تمت دعوتك من قبل الإدارة لتكون موظفًا في داشبورد إدارة تطبيق Job and Freelancer application.</p>--}}
{{--        <p>لبدء استخدام حسابك، يرجى الدخول إلى الرابط التالي:</p>--}}
{{--        <p><a href="{{ $link }}" target="_blank">{{ $link }}</a></p>--}}
{{--        <p>وستكون هذه هي كلمة السر الخاصة بك: <strong>{{ $password }}</strong></p>--}}
{{--        <p>نوصي بتغيير كلمة السر هذه فور الدخول إلى حسابك للمرة الأولى.</p>--}}
{{--    </div>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
