<!DOCTYPE html>

<html>

<head>
    <title>Login</title>
</head>

<body>

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<h2>Login</h2>

<a href="{{ route('auth.google') }}">
    Login with Google
</a>

</body>

</html>
