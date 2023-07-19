<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function countLogs(Request $request)
    {
        // Retrieve the filter parameters from the request
        $serviceNames = $request->serviceNames;
        $statusCode = $request->statusCode;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

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

        // Return the count as a JSON response
        return response()->json(['count' => $count]);
    }
}
