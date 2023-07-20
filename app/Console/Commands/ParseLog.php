<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Requests\LogLineRequest;
use App\Jobs\ProcessLogDataJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * A console command to parse a large log file and insert the data into the database.
 *
 * This command reads a large log file in chunks, validates each line, and inserts the log data into the database.
 * It utilizes the memory-efficient approach of processing the file in chunks to handle large files.
 */
class ParseLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:parse {file : The path to the log file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse a large log file and insert the data into the database';

    /**
     * The number of lines to process in each chunk.
     *
     * @var int
     */
    protected $chunkSize = 1000;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        try {
            // Validate the log file
            $this->validateLogFile($filePath);

            // Open the log file for reading
            $file = fopen($filePath, 'r');
            if (!$file) {
                $this->error('Unable to open the log file: ' . $filePath);
                return 1;
            }

            $lineCount = 0;
            $logData = [];

            // Process the file in chunks
            while (($line = fgets($file)) !== false) {
                $lineCount++;

                // Parse and validate each log line before inserting
                try {
                    $parsedLogLine = $this->parseLogLine($line, $lineCount);
                    // $parsedLogLine['line_number'] = $this->getFileLineNumber($line, $file);
                    $validatedData = $this->validateLogLine($parsedLogLine);
                    $logData[] = $validatedData;

                    // If the chunk size is reached, dispatch the log data to the job queue for insertion
                    if ($lineCount % $this->chunkSize === 0) {
                        $this->dispatchJob($logData);
                        $logData = [];
                    }
                } catch (\Exception $e) {
                    $this->error('Invalid log line: ' . $line);
                    continue;
                }
            }

            // Insert any remaining log data
            if (!empty($logData)) {
                $this->dispatchJob($logData);
            }

            // Close the log file
            fclose($file);

            $this->info('Log parsing completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Validate the log file.
     *
     * @param string $filePath
     * @return void
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    private function validateLogFile($filePath)
    {
        // Validate if the file exists
        if (!file_exists($filePath)) {
            throw new FileException('Log file not found: ' . $filePath);
        }

        // Implement additional validation rules for the log file, such as file size, extension, or virus scanning
        // You can utilize Laravel's Filesystem functions or any other validation library or service as per your needs
        // Throw a FileException with an appropriate message if the validation fails
    }

    /**
     * Parse a single log line to match the database schema.
     *
     * @param string $line
     * @return array
     * @throws \Exception
     */
    private function parseLogLine($line, $lineNumber)
    {
        $parts = explode(' ', $line);

        // Extract the necessary information from the log line
        $serviceName = trim($parts[0], '-');
        $loggedAt = $this->parseLoggedAt(trim($parts[2], '[]'));
        $method = trim($parts[3], '"');
        $endpoint = trim($parts[4], '"');
        $protocol = trim($parts[5], '"');
        $status = (int) trim($parts[6], '\r\n');

        // Create an array with the parsed log data
        $parsedLogLine = [
            'log_file_name' => basename($this->argument('file')),
            'file_last_updated_at' => Carbon::createFromTimestamp(filemtime($this->argument('file')))->toDateTimeString(),
            'line_number' => $lineNumber,
            'service_name' => $serviceName,
            'logged_at' => $loggedAt,
            'method' => $method,
            'endpoint' => $endpoint,
            'protocol' => $protocol,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $parsedLogLine;
    }

    /**
     * Parse the logged_at timestamp from the log line.
     *
     * @param string $datePart
     * @param string $timePart
     * @return string
     */
    private function parseLoggedAt($dateTime)
    {
        $dateTime = explode(':', $dateTime, 2);
        $datePart = $dateTime[0];
        $timePart = $dateTime[1];

        // Assuming the date and time parts are in the format: 17/Sep/2022:10:33:59
        $formattedDate = Carbon::createFromFormat('d/M/Y', $datePart)->format('Y-m-d'); // Change the format for proper formatting
        $formattedTime = $timePart;

        return $formattedDate . ' ' . $formattedTime;
    }

/**
 * Validate a single log line using a request-style validation.
 *
 * @param array $parsedLogLine
 * @return array|null
 * @throws \Exception
 */
private function validateLogLine($parsedLogLine)
{
    $validator = Validator::make($parsedLogLine, [
        'log_file_name' => 'required|string|max:255',
        'file_last_updated_at' => 'required|date_format:Y-m-d H:i:s',
        'line_number' => 'required|integer|min:1',
        'service_name' => 'required|string|max:255',
        'logged_at' => 'required|date_format:Y-m-d H:i:s',
        'method' => 'required|string|max:10',
        'endpoint' => 'required|string|max:255',
        'protocol' => 'required|string|max:20',
        'status' => 'required|integer|min:100|max:599',
    ]);

    if ($validator->fails()) {
        // If validation fails, log the errors and return null to skip this line
        $this->error('Invalid log line: ' . $validator->errors()->first());
    }

    // If validation succeeds, return the validated data
    return $validator->validated();
}

    /**
     * Dispatch the job to insert log data into the database.
     *
     * @param array $logData
     * @return void
     */
    private function dispatchJob($logData)
    {
        $job = new ProcessLogDataJob($logData);
        dispatch($job);
    }
}
