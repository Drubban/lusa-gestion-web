@extends('layouts.base')
@section('body')
<div class="d-grid" style="min-height:100vh;place-items:center">
    <div class="text-center p-4">
        <span class="brand-mark" style="font-size:2rem">LUSA</span>
        <h1 class="display-6 mt-4 mb-2">@yield('code')</h1>
        <p class="text-muted mb-4">@yield('message')</p>
        <a href="{{ url('/admin') }}" class="btn btn-primary">Volver al inicio</a>
    </div>
</div>
@endsection
