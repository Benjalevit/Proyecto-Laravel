@extends('layouts.app')

@section('title', 'Historial')

@section('gap', '14px')

@section('content')
<h1 style="margin: 0; font-size: 26px">Historial</h1>
<div style="display: flex; flex-direction: column; gap: 10px">
@forelse ($partidas as $partida)
<div style="padding: 14px; background: #FFFFFF; border-radius: 12px; border: 1px solid #D6D3CC; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px">
<div style="display: flex; flex-direction: column; gap: 4px">
@if ($partida->resultado === 'gano')
<span style="font-size: 16px; font-weight: 600; color: #15803D">Ganaste</span>
@elseif ($partida->resultado === 'perdio')
<span style="font-size: 16px; font-weight: 600; color: #B91C1C">Perdiste</span>
@else
<span style="font-size: 16px; font-weight: 600; color: #4B4B4B">Empate</span>
@endif
<span style="font-size: 14px; color: #4B4B4B">{{ ucfirst($partida->jugada_jugador) }} vs {{ ucfirst($partida->jugada_ia) }} · {{ $partida->created_at->format('d/m') }}</span>
</div>
<div style="display: flex; gap: 8px">
{{-- Editar recarga el historial con ?editar=id para abrir el formulario en esta tarjeta --}}
<form method="GET" action="{{ route('historial') }}" style="margin: 0">
<input type="hidden" name="editar" value="{{ $partida->id }}">
<button type="submit" style="height: 44px; min-width: 44px; padding: 0 10px; border: 1px solid #1F1F1F; border-radius: 8px; background: #FFFFFF; font-size: 14px">Editar</button>
</form>
<form method="POST" action="{{ route('partidas.destroy', $partida) }}" style="margin: 0" onsubmit="return confirm('¿Eliminar esta partida?')">
@csrf
@method('DELETE')
<button type="submit" style="height: 44px; min-width: 44px; padding: 0 10px; border: 1px solid #B91C1C; border-radius: 8px; background: #FFFFFF; font-size: 14px; color: #B91C1C">Eliminar</button>
</form>
</div>
@if ((int) request('editar') === $partida->id)
<form method="POST" action="{{ route('partidas.update', $partida) }}" style="margin: 0; width: 100%; display: flex; flex-direction: column; gap: 8px; font-size: 14px">
@csrf
@method('PUT')
<label>Tu jugada
<select name="jugada_jugador" style="height: 36px; margin-left: 6px; padding: 0 6px; border: 1px solid #D6D3CC; border-radius: 8px; background: #FFFFFF">
@foreach (['piedra', 'papel', 'tijera'] as $jugada)
<option value="{{ $jugada }}" @selected(old('jugada_jugador', $partida->jugada_jugador) === $jugada)>{{ ucfirst($jugada) }}</option>
@endforeach
</select>
</label>
<label>Jugada de la IA
<select name="jugada_ia" style="height: 36px; margin-left: 6px; padding: 0 6px; border: 1px solid #D6D3CC; border-radius: 8px; background: #FFFFFF">
@foreach (['piedra', 'papel', 'tijera'] as $jugada)
<option value="{{ $jugada }}" @selected(old('jugada_ia', $partida->jugada_ia) === $jugada)>{{ ucfirst($jugada) }}</option>
@endforeach
</select>
</label>
@if ($errors->any())
<span style="color: #B91C1C">{{ $errors->first() }}</span>
@endif
<div style="display: flex; gap: 8px">
<button type="submit" style="height: 44px; padding: 0 10px; border: 1px solid #1F1F1F; border-radius: 8px; background: #1F1F1F; color: #FFFFFF; font-size: 14px">Guardar</button>
<a href="{{ route('historial') }}" style="height: 44px; line-height: 44px; padding: 0 10px; border: 1px solid #D6D3CC; border-radius: 8px; color: #1F1F1F; text-decoration: none">Cancelar</a>
</div>
</form>
@endif
</div>
@empty
<p style="margin: 0; font-size: 14px; color: #4B4B4B">Aún no hay partidas.</p>
@endforelse
</div>
@endsection
