<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Log Model representing a log entry from a microservice.
 *
 * This model provides access to the logs table in the database.
 */
class Log extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_file_name',
        'file_last_updated_at',
        'line_number',
        'service_name',
        'logged_at',
        'method',
        'endpoint',
        'protocol',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'file_last_updated_at',
        'logged_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Retrieve the formatted logged_at attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getLoggedAtAttribute($value)
    {
        // Format the logged_at attribute as desired (e.g., to a specific date and time format)
        return $this->asDateTime($value)->format('Y-m-d H:i:s');
    }

        /**
     * Scope a query to filter logs by log file name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $logFileName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLogFileName($query, $logFileName)
    {
        // Apply a filter to retrieve logs by log file name
        return $query->where('log_file_name', $logFileName);
    }

    /**
     * Scope a query to filter logs by file last updated at.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $fileLastUpdatedAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFileLastUpdatedAt($query, $fileLastUpdatedAt)
    {
        // Apply a filter to retrieve logs by file last updated at
        return $query->where('file_last_updated_at', $fileLastUpdatedAt);
    }

    /**
     * Scope a query to filter logs by line number.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $lineNumber
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLineNumber($query, $lineNumber)
    {
        // Apply a filter to retrieve logs by line number
        return $query->where('line_number', $lineNumber);
    }

    /**
     * Scope a query to filter logs by service name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $serviceName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByServiceName($query, $serviceName)
    {
        // Apply a filter to retrieve logs by service name
        return $query->where('service_name', $serviceName);
    }

    /**
     * Scope a query to filter logs by logged_at.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $loggedAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLoggedAt($query, $loggedAt)
    {
        // Apply a filter to retrieve logs by logged_at
        return $query->where('logged_at', $loggedAt);
    }

    /**
     * Scope a query to filter logs by method.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $method
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMethod($query, $method)
    {
        // Apply a filter to retrieve logs by method
        return $query->where('method', $method);
    }

    /**
     * Scope a query to filter logs by endpoint.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $endpoint
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEndpoint($query, $endpoint)
    {
        // Apply a filter to retrieve logs by endpoint
        return $query->where('endpoint', $endpoint);
    }

    /**
     * Scope a query to filter logs by protocol.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $protocol
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProtocol($query, $protocol)
    {
        // Apply a filter to retrieve logs by protocol
        return $query->where('protocol', $protocol);
    }
    
    /**
     * Scope a query to filter logs by status code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $statusCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatusCode($query, $statusCode)
    {
        // Apply a filter to retrieve logs by status code
        return $query->where('status', $statusCode);
    }
}
