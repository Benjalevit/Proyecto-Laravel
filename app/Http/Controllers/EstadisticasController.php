<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $partidas = $request->user()->partidas()->orderByDesc('id')->get();
        $total = $partidas->count();

        $porResultado = $partidas->countBy('resultado');
        $porJugada = $partidas->countBy('jugada_jugador');

        $jugadas = collect(['piedra', 'papel', 'tijera'])
            ->map(fn ($m) => [
                'nombre' => ucfirst($m),
                'pct'    => $total ? (int) round(($porJugada[$m] ?? 0) * 100 / $total) : 0,
            ])
            ->sortByDesc('pct')
            ->values();

        $racha = 0;
        foreach ($partidas as $p) {
            if ($p->resultado !== 'gano') {
                break;
            }
            $racha++;
        }

        return view('estadisticas', [
            'victorias' => $porResultado['gano'] ?? 0,
            'derrotas'  => $porResultado['perdio'] ?? 0,
            'empates'   => $porResultado['empate'] ?? 0,
            'jugadas'   => $jugadas,
            'racha'     => $racha,
        ]);
    }
}