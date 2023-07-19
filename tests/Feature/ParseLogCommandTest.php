<?php

use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParseLogCommandTest extends TestCase
{
    use RefreshDatabase, InteractsWithConsole;

    /**
     * Test the parse log command.
     *
     * @return void
     */
    public function testParseLogCommand(): void
    {
        $logFilePath = './storage/logs/logs.txt';

        // Run the parse log command
        $this->artisan('log:parse', [
            'file' => $logFilePath,
        ])
        ->expectsOutput('Log parsing completed successfully.')
        ->assertExitCode(0);

        // Assert the data in the database based on your expectations
        $this->assertDatabaseCount('logs', 20); // Example: Assert that there are 1000 records in the 'logs' table
        $this->assertDatabaseHas('logs', ['service_name' => 'invoice-service']); // Example: Assert that a record with 'service_name' = 'invoice-service' exists in the 'logs' table
        // Add more assertions as per your specific requirements
    }
}
