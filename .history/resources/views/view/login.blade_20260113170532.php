@extends('layout.app')

@section('content')

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Google Login Button (NO STYLE CHANGE) --}}
    <a href="{{ route('auth.google') }}">
        Login with Google
    </a>

@endsection
