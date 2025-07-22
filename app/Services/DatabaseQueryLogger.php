<?php

namespace App\Services;

use Closure;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseQueryLogger
{
    /**
     * @var array
     */
    protected $queryLog = [];

    /**
     * @var bool
     */
    protected $loggingQueries = true;

    /**
     * Execute a query and log it.
     *
     * @param string $query
     * @param array $bindings
     * @param Closure $callback
     * @return mixed
     * @throws QueryException
     */
    public function runQuery($query, $bindings, Closure $callback)
    {
        try {
            $startTime = microtime(true);
            $result = $callback($query, $bindings);
            $time = microtime(true) - $startTime;

            $this->logQuery($query, $bindings, $time);

            return $result;
        } catch (Exception $e) {
            throw new QueryException(
                $query,
                $this->prepareBindings($bindings),
                $e
            );
        }
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param string $query
     * @param array $bindings
     * @param float|null $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null)
    {
        if ($this->loggingQueries) {
            // Format the query with bindings for better readability
            $formattedQuery = $this->formatQuery($query, $bindings);
            
            $logEntry = [
                'query' => $formattedQuery,
                'bindings' => $bindings,
                'time' => $time ? round($time * 1000, 2) . 'ms' : null,
                'connection' => DB::connection()->getName(),
                'timestamp' => now()->toDateTimeString(),
            ];

            $this->queryLog[] = $logEntry;

            // Log to Laravel's logging system
            Log::channel('queries')->info('Database Query', $logEntry);

            // If in debug mode, also log to the main log
            if (config('app.debug')) {
                Log::debug('Database Query', $logEntry);
            }
        }
    }

    /**
     * Format the query with bindings for better readability.
     *
     * @param string $query
     * @param array $bindings
     * @return string
     */
    protected function formatQuery($query, $bindings)
    {
        $formattedQuery = $query;
        
        foreach ($bindings as $key => $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $formattedQuery = preg_replace('/\?/', $value, $formattedQuery, 1);
        }

        return $formattedQuery;
    }

    /**
     * Get the query log.
     *
     * @return array
     */
    public function getQueryLog()
    {
        return $this->queryLog;
    }

    /**
     * Clear the query log.
     *
     * @return void
     */
    public function clearQueryLog()
    {
        $this->queryLog = [];
    }

    /**
     * Enable query logging.
     *
     * @return void
     */
    public function enableQueryLog()
    {
        $this->loggingQueries = true;
    }

    /**
     * Disable query logging.
     *
     * @return void
     */
    public function disableQueryLog()
    {
        $this->loggingQueries = false;
    }

    /**
     * Prepare the query bindings for logging.
     *
     * @param array $bindings
     * @return array
     */
    protected function prepareBindings(array $bindings)
    {
        return array_map(function ($binding) {
            if (is_object($binding)) {
                return (string) $binding;
            }

            return $binding;
        }, $bindings);
    }
} 