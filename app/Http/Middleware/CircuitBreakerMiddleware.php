<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CircuitBreakerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $serviceName)
    {
        $key = "circuit_{$serviceName}";

        $state = Cache::get($key, [
            'failures' => 0,
            'last_failure' => null,
            'open' => false,
        ]);

        // Circuit OPEN
        if ($state['open']) {

            // Cooldown passed → HALF-OPEN
            if ($state['last_failure'] && now()->diffInSeconds($state['last_failure']) >= 30) 
                return $this->attempt($next, $request, $key, $state);

            // if still in cooldown → return error
            return response()->json([
                'status' => 'blocked',
                'message' => "{$serviceName} temporarily unavailable"
            ], 503);
        }

        // Circuit CLOSED
        return $this->attempt($next, $request, $key, $state);
    }

    protected function attempt($next, $request, $key, $state)
    {
        try {
            $response = $next($request);

            // Success → close circuit
            Cache::put($key, [
                'failures' => 0,
                'last_failure' => null,
                'open' => false,
            ]);

            return $response;

        } catch (\Exception $e) {

            // Failure → update state
            $state['failures']++;
            $state['last_failure'] = now();

            // Open circuit if failures exceed threshold
            if ($state['failures'] >= 3) 
                $state['open'] = true;

            // Save state and return error
            Cache::put($key, $state);

            return unavailableServiceFailure([] ,"Service is currently unavailable. Please try again later.");
        }
    }
}
