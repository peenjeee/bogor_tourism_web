<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FlaskProxyController extends Controller
{
    private const ALLOWED_ENDPOINTS = [
        'places',
        'recommendations',
        'search',
    ];

    public function __invoke(Request $request, ?string $path = null)
    {
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)->withHeaders($this->corsHeaders());
        }

        if (! $request->isMethod('GET') && ! $request->isMethod('POST')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Method not allowed.',
            ], 405)->withHeaders($this->corsHeaders());
        }

        $path = trim((string) $path, '/');
        $firstSegment = explode('/', $path)[0] ?? '';

        if ($firstSegment === '' || ! in_array($firstSegment, self::ALLOWED_ENDPOINTS, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Endpoint not found.',
            ], 404)->withHeaders($this->corsHeaders());
        }

        $baseUrl = rtrim((string) config('services.flask.base_url'), '/');
        $targetUrl = "{$baseUrl}/api/{$path}";
        $timeout = (int) config('services.flask.timeout', 30);

        try {
            $client = Http::timeout($timeout)->acceptJson();
            $response = $request->isMethod('GET')
                ? $client->get($targetUrl, $request->query())
                : $client->asJson()->post($targetUrl, $request->json()->all() ?: $request->all());

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type', 'application/json'))
                ->withHeaders($this->corsHeaders());
        } catch (Throwable $exception) {
            Log::error('Flask API proxy failed', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Flask API is unavailable.',
            ], 502)->withHeaders($this->corsHeaders());
        }
    }

    private function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Accept',
        ];
    }
}