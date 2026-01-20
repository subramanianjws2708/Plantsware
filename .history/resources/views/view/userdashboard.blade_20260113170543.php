@extends('layouts.app')

@section('content')

    <h2>Welcome {{ auth()->user()->name }}</h2>
    <p>Email: {{ auth()->user()->email }}</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

@endsection
