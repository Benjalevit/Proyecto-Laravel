@extends('layouts.app')
@section('title', 'Estadísticas')
@section('class', 'stack-16')
@section('content')
<h1>Estadísticas</h1>

<div class="stats-grid">
    <div class="stat"><b class="win">{{ $victorias }}</b><span>Victorias</span></div>
    <div class="stat"><b class="lose">{{ $derrotas }}</b><span>Derrotas</span></div>
    <div class="stat"><b class="draw">{{ $empates }}</b><span>Empates</span></div>
</div>

<div class="card card-col">
    <span style="font-size:16px;font-weight:600">Jugadas más usadas</span>
    @foreach ($jugadas as $j)
        <div style="display:flex;flex-direction:column;gap:4px">
            <span style="font-size:14px">{{ $j['nombre'] }} · {{ $j['pct'] }}%</span>
            <div class="bar"><div style="width: {{ $j['pct'] }}%"></div></div>
        </div>
    @endforeach
</div>

<p class="muted">Tu racha actual: {{ $racha }} {{ $racha === 1 ? 'victoria seguida' : 'victorias seguidas' }}.</p>

<form method="POST" action="{{ route('logout') }}" style="margin-top:8px">
    @csrf
    <button type="submit" class="btn-sm">Cerrar sesión</button>
</form>
@endsection
