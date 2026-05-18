<?php
namespace App\Services\Aop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    public function log(array $data): void
    {
        Log::channel(config('aop.performance_monitoring.log_channel'))
            ->info('API Performance Monitoring', $data);
    }

    public function buildLogData(
        Request $request,
        string $aspectName,
        float $durationMs,
        int $memoryMb,
        int $statusCode,
        int $dbQueriesCount,
        float $dbQueriesTimeMs
    ): array {
        return [
            'aspect'             => $aspectName,
            'route'              => optional($request->route())->getName(),
            'method'             => $request->method(),
            'uri'                => $request->path(),
            'status_code'        => $statusCode,
            'duration_ms'        => round($durationMs, 2),
            'memory_peak_mb'     => $memoryMb,
            'db_queries_count'   => $dbQueriesCount,
            'db_queries_time_ms' => round($dbQueriesTimeMs, 2),
            'user_id'            => optional($request->user())->id,
            'ip'                 => $request->ip(),
            'is_slow'            => $durationMs >= config('aop.performance_monitoring.slow_request_ms'),
            'created_at'         => now()->toDateTimeString(),
        ];
    }
}
