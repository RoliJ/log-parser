<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Log;
use App\Http\Requests\LogLineRequest;
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
                    $parsedLogLine['line_number'] = $this->getFileLineNumber($line, $file);
                    $validatedData = $this->validateLogLine($parsedLogLine);
                    $logData[] = $validatedData;
                } catch (\Exception $e) {
                    $this->error('Invalid log line: ' . $line);
                    continue;
                }

                // If the chunk size is reached, insert the log data into the database
                if ($lineCount % $this->chunkSize === 0) {
                    $this->insertLogData($logData);
                    $logData = [];
                }
            }

            // Insert any remaining log data
            if (!empty($logData)) {
                $this->insertLogData($logData);
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
    private function parseLogLine($line, $lineNumber = null)
    {
        $parts = explode(' ', $line);

        // Extract the necessary information from the log line
        $serviceName = trim($parts[0], '-');
        $loggedAt = $this->parseLoggedAt($parts[2], $parts[3]);
        $method = trim($parts[4], '"');
        $endpoint = trim($parts[5], '"');
        $protocol = trim($parts[6], '"');
        $status = (int) $parts[7];

        // Create an array with the parsed log data
        $parsedLogLine = [
            'log_file_name' => basename($this->argument('file')),
            'file_last_updated_at' => filemtime($this->argument('file')),
            'line_number' => $lineNumber,
            'service_name' => $serviceName,
            'logged_at' => $loggedAt,
            'method' => $method,
            'endpoint' => $endpoint,
            'protocol' => $protocol,
            'status' => $status,
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
    private function parseLoggedAt($datePart, $timePart)
    {
        // Assuming the date and time parts are in the format: [17/Sep/2022:10:33:59]
        $formattedDate = str_replace('/', '-', substr($datePart, 1)); // Replace slashes with dashes for proper formatting
        $formattedTime = str_replace(':', '-', substr($timePart, 0, -1)); // Replace colons with dashes for proper formatting

        return $formattedDate . ' ' . $formattedTime;
    }

    /**
     * Get the line number of a log line in the file.
     *
     * @param string $line
     * @param resource $file
     * @return int|null
     */
    private function getFileLineNumber($line, $file)
    {
        rewind($file); // Reset the file pointer to the beginning

        $lineNumber = 1;
        while (($fileLine = fgets($file)) !== false) {
            if ($fileLine === $line) {
                return $lineNumber;
            }

            $lineNumber++;
        }

        return null;
    }

    /**
     * Validate a single log line using a request-style validation.
     *
     * @param array $parsedLogLine
     * @return array
     * @throws \Exception
     */
    private function validateLogLine($parsedLogLine)
    {
        $request = new LogLineRequest();
        $request->replace($parsedLogLine);

        // Validate the request using the defined validation rules in the LogLineRequest class
        $validatedData = $request->validate();

        return $validatedData;
    }

    /**
     * Insert the log data into the database.
     *
     * @param array $logData
     * @return void
     */
    private function insertLogData($logData)
    {
        // Use the Log model's insert method to efficiently insert the log data in bulk
        Log::insert($logData);
    }
}
