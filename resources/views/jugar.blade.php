@extends('layouts.app')

@section('title', 'Jugar')

@section('content')
<h1 style="margin: 0; font-size: 26px">Nueva ronda</h1>
<p style="margin: 0; font-size: 14px; color: #4B4B4B">Ronda {{ $ronda }} · Marcador: tú {{ $victorias }} – IA {{ $derrotas }}</p>
<div style="padding: 16px; border-radius: 12px; background: #FFF1E6; border: 1px solid #EA580C; display: flex; flex-direction: column; gap: 6px">
<span style="font-size: 13px; font-weight: 600; color: #9A3412">Sugerencia de la IA</span>
@if ($prediccion)
<span style="font-size: 17px; font-weight: 600">Esta vez, la IA cree que tiras {{ $prediccion }}.</span>
@endif
<span style="font-size: 14px; color: #4B4B4B">{{ $comentario }}</span>
</div>
<p style="margin: 8px 0 0; font-size: 16px; font-weight: 600">Elige tu jugada</p>
<form method="POST" action="{{ route('partidas.store') }}" style="margin: 0; display: flex; flex-direction: column; gap: 12px">
@csrf
<button type="submit" name="jugada" value="piedra" style="height: 72px; border: 2px solid #1F1F1F; border-radius: 12px; background: #FFFFFF; font-size: 18px; font-weight: 600; color: #1F1F1F">Piedra</button>
<button type="submit" name="jugada" value="papel" style="height: 72px; border: 2px solid #1F1F1F; border-radius: 12px; background: #FFFFFF; font-size: 18px; font-weight: 600; color: #1F1F1F">Papel</button>
<button type="submit" name="jugada" value="tijera" style="height: 72px; border: 2px solid #1F1F1F; border-radius: 12px; background: #FFFFFF; font-size: 18px; font-weight: 600; color: #1F1F1F">Tijera</button>
</form>
@error('jugada')
<p style="margin: 0; font-size: 14px; color: #B91C1C">{{ $message }}</p>
@enderror
@isset($ultima)
<p style="margin: 4px 0 0; font-size: 14px; color: #4B4B4B">Último resultado: {{ ['gano' => 'ganaste', 'perdio' => 'perdiste', 'empate' => 'empataste'][$ultima->resultado] }} con {{ $ultima->jugada_jugador }} contra {{ $ultima->jugada_ia }}.</p>
@endisset
@endsection
