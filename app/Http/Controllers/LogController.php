<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Http\Requests\CountLogsRequest;
use App\Http\Resources\LogCountResource;
use App\Models\Log;

class LogController extends Controller
{
    public function countLogs(CountLogsRequest $request)
    {
        // Retrieve the validated filter parameters from the request
        $serviceNames = $request->serviceNames;
        $statusCode = $request->statusCode;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Generate a unique cache key based on the request parameters
        $cacheKey = md5($request->fullUrl() . serialize($request->all()));

        // Check if the result is already cached
        if (Cache::has($cacheKey)) {
            // Retrieve the cached result and return it
            $result = Cache::get($cacheKey);
            return response()->json($result);
        }

        // Build the query based on the filter parameters
        $query = Log::query();

        if ($serviceNames) {
            $query->whereIn('service_name', $serviceNames);
        }

        if ($statusCode) {
            $query->where('status', $statusCode);
        }

        if ($startDate) {
            $query->whereDate('logged_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('logged_at', '<=', $endDate);
        }

        // Perform the count query
        $count = $query->count();

        // Create the response payload
        $response = new LogCountResource($count);

        // Cache the response with a TTL (time-to-live) of 5 minutes
        Cache::put($cacheKey, $response, 300);

        // Return the response as JSON
        return $response;
    }
}
