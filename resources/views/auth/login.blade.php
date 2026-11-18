@extends('layouts.app')
@section('title', 'Login')
@section('class', 'auth')
@section('content')
<h1>Cachipún IA</h1>
<p class="muted" style="font-size:16px">Juega contra una IA que aprende de tus patrones.</p>

<form method="POST" action="{{ url('/login') }}" style="display:flex;flex-direction:column;gap:20px;margin-top:24px">
    @csrf
    <div class="field">
        <label for="mail">Correo</label>
        <input id="mail" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.cl" required>
        @error('email')<p class="error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="pass">Contraseña</label>
        <input id="pass" name="password" type="password" placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn-primary">Entrar</button>
</form>
<p style="margin:8px 0 0;font-size:14px;text-align:center">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
@endsection
