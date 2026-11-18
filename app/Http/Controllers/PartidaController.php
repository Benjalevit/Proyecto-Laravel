<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartidaController extends Controller
{
    private const JUGADAS = ['piedra', 'papel', 'tijera'];

    // Qué jugada le gana a cada una
    private const LE_GANA = [
        'piedra' => 'papel',
        'papel' => 'tijera',
        'tijera' => 'piedra',
    ];

    public function jugar(Request $request)
    {
        $partidas = $request->user()->partidas();
        $sugerencia = $this->pedirSugerencia($request);

        // La predicción se guarda para usarla cuando el jugador elija su jugada
        session(['prediccion' => $sugerencia['prediccion']]);

        return view('jugar', [
            'ronda' => $partidas->count() + 1,
            'victorias' => $request->user()->partidas()->where('resultado', 'gano')->count(),
            'derrotas' => $request->user()->partidas()->where('resultado', 'perdio')->count(),
            'prediccion' => $sugerencia['prediccion'],
            'comentario' => $sugerencia['comentario'],
            'ultima' => $request->user()->partidas()->latest('id')->first(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jugada' => ['required', 'in:piedra,papel,tijera'],
        ], [
            'jugada.required' => 'Elige una jugada.',
            'jugada.in' => 'La jugada debe ser piedra, papel o tijera.',
        ]);

        $prediccion = session()->pull('prediccion');

        // Si la IA no entregó predicción, juega al azar como respaldo
        $jugadaIa = $prediccion
            ? self::LE_GANA[$prediccion]
            : self::JUGADAS[array_rand(self::JUGADAS)];

        $request->user()->partidas()->create([
            'jugada_jugador' => $validated['jugada'],
            'jugada_ia' => $jugadaIa,
            'resultado' => $this->resultado($validated['jugada'], $jugadaIa),
        ]);

        return redirect()->route('jugar');
    }

    public function historial(Request $request)
    {
        return view('historial', [
            'partidas' => $request->user()->partidas()->latest('id')->get(),
        ]);
    }

    public function update(Request $request, Partida $partida)
    {
        $this->asegurarDueno($request, $partida);

        $validated = $request->validate([
            'jugada_jugador' => ['required', 'in:piedra,papel,tijera'],
            'jugada_ia' => ['required', 'in:piedra,papel,tijera'],
        ], [
            'jugada_jugador.required' => 'Elige tu jugada.',
            'jugada_jugador.in' => 'Tu jugada debe ser piedra, papel o tijera.',
            'jugada_ia.required' => 'Elige la jugada de la IA.',
            'jugada_ia.in' => 'La jugada de la IA debe ser piedra, papel o tijera.',
        ]);

        $partida->update([
            'jugada_jugador' => $validated['jugada_jugador'],
            'jugada_ia' => $validated['jugada_ia'],
            'resultado' => $this->resultado($validated['jugada_jugador'], $validated['jugada_ia']),
        ]);

        return redirect()->route('historial');
    }

    public function destroy(Request $request, Partida $partida)
    {
        $this->asegurarDueno($request, $partida);

        $partida->delete();

        return redirect()->route('historial');
    }

    // Cada jugador solo puede editar o borrar sus propias partidas
    private function asegurarDueno(Request $request, Partida $partida): void
    {
        abort_unless((int) $partida->user_id === (int) $request->user()->id, 403);
    }

    private function resultado(string $jugador, string $ia): string
    {
        if ($jugador === $ia) {
            return 'empate';
        }

        return self::LE_GANA[$ia] === $jugador ? 'gano' : 'perdio';
    }

    /**
     * Envía el historial a Groq y devuelve la jugada que la IA cree que hará
     * el jugador junto con un comentario. Si algo falla, la predicción es null.
     */
    private function pedirSugerencia(Request $request): array
    {
        $respaldo = [
            'prediccion' => null,
            'comentario' => 'La IA no respondió. Esta ronda jugará al azar.',
        ];

        $partidas = $request->user()->partidas()->latest('id')->take(20)->get()->reverse();

        $historial = $partidas->isEmpty()
            ? 'El jugador todavía no tiene partidas.'
            : $partidas->map(fn ($p) => "jugador: {$p->jugada_jugador}, IA: {$p->jugada_ia}, resultado del jugador: {$p->resultado}")
                ->implode("\n");

        $rol = 'Eres la IA rival en un juego de cachipún (piedra, papel o tijera). '
            . 'Analiza el historial del jugador, ordenado de la partida más antigua a la más reciente, '
            . 'y predice qué jugada hará en la próxima ronda. Responde únicamente con un JSON '
            . 'con este formato: {"prediccion": "piedra|papel|tijera", "comentario": "..."}. '
            . 'El comentario debe ser una frase corta en español, dirigida al jugador, '
            . 'sobre el patrón que notas. Sin markdown ni texto adicional.';

        try {
            $respuesta = Http::withToken(config('services.groq.key'))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $rol],
                        ['role' => 'user', 'content' => $historial],
                    ],
                    // Sin esto el modelo gasta todos los tokens razonando y responde vacío
                    'reasoning_effort' => 'low',
                ]);
        } catch (ConnectionException $e) {
            Log::error('No se pudo conectar con Groq', ['error' => $e->getMessage()]);

            return $respaldo;
        }

        if ($respuesta->failed()) {
            Log::error('Groq devolvió un error', [
                'status' => $respuesta->status(),
                'body' => $respuesta->json(),
            ]);

            return $respaldo;
        }

        $contenido = $respuesta->json('choices.0.message.content');

        // Se valida el formato antes de usarlo: la IA puede responder distinto a lo pedido
        preg_match('/\{.*\}/s', (string) $contenido, $coincidencia);
        $datos = json_decode($coincidencia[0] ?? '', true);

        if (! is_array($datos)
            || ! in_array($datos['prediccion'] ?? null, self::JUGADAS, true)
            || blank($datos['comentario'] ?? null)) {
            Log::warning('Groq respondió con un formato inesperado', [
                'body' => $respuesta->json(),
            ]);

            return $respaldo;
        }

        return [
            'prediccion' => $datos['prediccion'],
            'comentario' => $datos['comentario'],
        ];
    }
}
