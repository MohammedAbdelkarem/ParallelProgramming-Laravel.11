<?php
namespace App\Http\Middleware\Aop;

use App\Services\Aop\PerformanceMonitoringService;
use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoringMiddleware
{
    public function __construct(
        private readonly PerformanceMonitoringService $performanceMonitoringService
    ) {}

    public function handle(Request $request, Closure $next, string $aspectName = 'api.performance'): Response
    {
        if (! config('aop.performance_monitoring.enabled')) {
            return $next($request);
        }

        $startTime = microtime(true);

        $dbQueriesCount  = 0;
        $dbQueriesTimeMs = 0;

        DB::listen(function (QueryExecuted $query) use (&$dbQueriesCount, &$dbQueriesTimeMs) {
            $dbQueriesCount++;
            $dbQueriesTimeMs += $query->time;
        });

        /** @var Response $response */
        $response = $next($request);

        $durationMs = (microtime(true) - $startTime) * 1000;
        $memoryMb   = (int) round(memory_get_peak_usage(true) / 1024 / 1024);

        $this->performanceMonitoringService->log(
            $this->performanceMonitoringService->buildLogData(
                request: $request,
                aspectName: $aspectName,
                durationMs: $durationMs,
                memoryMb: $memoryMb,
                statusCode: $response->getStatusCode(),
                dbQueriesCount: $dbQueriesCount,
                dbQueriesTimeMs: $dbQueriesTimeMs
            )
        );

        return $response;
    }
}
