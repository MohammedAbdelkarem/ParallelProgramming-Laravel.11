<?php

namespace App\Http\Controllers\Administration\Integrations;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Rabet\BayanService;
use App\Services\Rabet\RabetBayanHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class RabetConnectionController extends Controller
{
    /**
     * Lightweight connectivity probe for Wasl/Bayan (DNS + HTTP).
     * Does not expose secrets. No authentication required.
     */
    public function ping(BayanService $bayan)
    {
        $waslUrl  = rtrim((string) config('services.rabet.wasl_url'), '/');
        $bayanUrl = rtrim((string) config('services.rabet.bayan_url'), '/');
        $timeout  = (int) config('services.rabet.http_timeout', 45);

        $headers = $this->rabetHeaders();

        $payload = [
            'config' => [
                'environment'            => config('services.rabet.environment'),
                'wasl_use_eff'           => (bool) config('services.rabet.wasl_use_eff', false),
                'http_timeout'           => $timeout,
                'wasl_url'               => $this->maskUrl($waslUrl),
                'bayan_url'              => $this->maskUrl($bayanUrl),
                'app_id_configured'      => filled(config('services.rabet.app_id')),
                'app_key_configured'     => filled(config('services.rabet.app_key')),
                'client_id_configured'   => filled(config('services.rabet.client_id')),
            ],
            'wasl'  => $this->probeHttpEndpoint('wasl', $waslUrl, $headers, $timeout),
            'bayan' => array_merge(
                $this->probeDns('bayan', $bayanUrl),
                $this->probeBayanApi($bayan)
            ),
        ];

        return success($payload, ApiMessages::MSG_SUCCESS);
    }

    /**
     * Sends a GET request to the configured Bayan base URL (e.g. https://bayan-stg.api.elm.sa).
     * Query: path (optional), default /api/v1/lookup/good-types — only paths under /api/v1/ are allowed.
     */
    public function bayanRequest(Request $request)
    {
        $base = rtrim((string) config('services.rabet.bayan_url'), '/');
        if ($base === '') {
            return response()->json([
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message'     => 'RABET_BAYAN_URL is not configured.',
                'data'        => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $path = $request->query('path', '/api/v1/lookup/good-types');
        if (! is_string($path) || strlen($path) > 512 || ! str_starts_with($path, '/api/v1/')
            || str_contains($path, '..')) {
            return response()->json([
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message'     => 'Invalid path. Use a path starting with /api/v1/ (max 512 chars, no ..).',
                'data'        => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $timeout = (int) config('services.rabet.http_timeout', 45);
        $started = microtime(true);

        try {
            $response = Http::withHeaders(RabetBayanHeaders::forRequest())
                ->baseUrl($base)
                ->timeout($timeout)
                ->connectTimeout(min(10, $timeout))
                ->get($path);
        } catch (\Throwable $e) {
            return success([
                'request_url'   => $base . $path,
                'bayan_base'    => $this->maskUrl($base),
                'path'          => $path,
                'http_ok'       => false,
                'http_status'   => null,
                'latency_ms'    => round((microtime(true) - $started) * 1000, 2),
                'error'         => $e->getMessage(),
                'body'          => null,
            ], ApiMessages::MSG_SUCCESS);
        }

        $latency = round((microtime(true) - $started) * 1000, 2);
        $rawBody = $response->body();
        $json = $response->json();

        return success([
            'request_url' => $base . $path,
            'bayan_base'  => $this->maskUrl($base),
            'path'        => $path,
            'http_ok'     => $response->successful(),
            'http_status' => $response->status(),
            'latency_ms'  => $latency,
            'error'       => $response->failed() ? 'http_' . $response->status() : null,
            'body'        => is_array($json) ? $json : mb_substr($rawBody, 0, 4000),
        ], ApiMessages::MSG_SUCCESS);
    }

    private function rabetHeaders(): array
    {
        $h = [
            'app_id'       => (string) config('services.rabet.app_id'),
            'app_key'      => (string) config('services.rabet.app_key'),
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
        $cid = config('services.rabet.client_id');
        if (filled($cid)) {
            $h['client-id'] = (string) $cid;
            $h['client_id'] = (string) $cid;
        }

        return $h;
    }

    private function maskUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['host'])) {
            return $url;
        }

        return ($parts['scheme'] ?? 'https') . '://' . $parts['host']
            . (isset($parts['port']) ? ':' . $parts['port'] : '')
            . ($parts['path'] ?? '');
    }

    /**
     * @return array{dns_resolves: bool, dns_error: ?string, host: ?string}
     */
    private function probeDns(string $_label, string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return [
                'dns_resolves' => false,
                'dns_error'    => 'invalid_or_empty_url',
                'host'         => null,
            ];
        }

        $ip = @gethostbyname($host);
        if ($ip === $host) {
            return [
                'dns_resolves' => false,
                'dns_error'    => 'could_not_resolve_host',
                'host'         => $host,
            ];
        }

        return [
            'dns_resolves' => true,
            'dns_error'    => null,
            'host'         => $host,
        ];
    }

    /**
     * @return array{dns_resolves: bool, dns_error: ?string, host: ?string, http_status: ?int, http_reachable: bool, latency_ms: ?float, http_error: ?string}
     */
    private function probeHttpEndpoint(string $label, string $url, array $headers, int $timeout): array
    {
        $dns = $this->probeDns($label, $url);
        if (! $dns['dns_resolves']) {
            return array_merge($dns, [
                'http_status'     => null,
                'http_reachable'  => false,
                'latency_ms'      => null,
                'http_error'      => 'skipped_no_dns',
            ]);
        }

        if ($url === '') {
            return array_merge($dns, [
                'http_status'     => null,
                'http_reachable'  => false,
                'latency_ms'      => null,
                'http_error'      => 'empty_url',
            ]);
        }

        $started = microtime(true);
        try {
            $response = Http::withHeaders($headers)
                ->timeout($timeout)
                ->connectTimeout(min(10, $timeout))
                ->get($url);
            $ms = round((microtime(true) - $started) * 1000, 2);

            return array_merge($dns, [
                'http_status'    => $response->status(),
                'http_reachable' => true,
                'latency_ms'     => $ms,
                'http_error'     => $response->failed() ? 'http_' . $response->status() : null,
            ]);
        } catch (\Throwable $e) {
            $ms = round((microtime(true) - $started) * 1000, 2);

            return array_merge($dns, [
                'http_status'    => null,
                'http_reachable' => false,
                'latency_ms'     => $ms,
                'http_error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return array{good_types_api_ok: bool, good_types_http_status: ?int, good_types_error: ?string, good_types_latency_ms: ?float}
     */
    private function probeBayanApi(BayanService $bayan): array
    {
        $started = microtime(true);
        $data = $bayan->getGoodTypes();
        $ms = round((microtime(true) - $started) * 1000, 2);

        return [
            'good_types_api_ok'        => $data !== null,
            'good_types_http_status'   => null,
            'good_types_error'         => $data === null ? 'getGoodTypes_returned_null_see_logs' : null,
            'good_types_latency_ms'    => $ms,
        ];
    }
}