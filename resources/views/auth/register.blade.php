@extends('layouts.app')
@section('title', 'Registro')
@section('class', 'auth')
@section('content')
<h1>Crear cuenta</h1>
<p class="muted" style="font-size:16px">Empieza a jugar contra la IA.</p>

<form method="POST" action="{{ url('/registro') }}" style="display:flex;flex-direction:column;gap:20px;margin-top:24px">
    @csrf
    <div class="field">
        <label for="mail">Correo</label>
        <input id="mail" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.cl" required>
        @error('email')<p class="error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="pass">Contraseña</label>
        <input id="pass" name="password" type="password" placeholder="Mínimo 8 caracteres" required>
        @error('password')<p class="error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="pass2">Repetir contraseña</label>
        <input id="pass2" name="password_confirmation" type="password" required>
    </div>
    <button type="submit" class="btn-primary">Registrarme</button>
</form>
<p style="margin:8px 0 0;font-size:14px;text-align:center">¿Ya tienes cuenta? <a href="{{ route('login') }}">Entrar</a></p>
@endsection
