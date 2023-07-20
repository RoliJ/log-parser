<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CountLogsRequest;
use App\Http\Resources\LogCountResource;
use App\Models\Log as LogModel;
use App\Models\User;
use Illuminate\Database\QueryException;
use Laravel\Passport\Passport;

class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * This method is called before each test method.
     * It creates a test data.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        // Create test data
        $logFilePath = './storage/logs/logs.txt';

        // Run the parse log command
        $this->artisan('log:parse', [
            'file' => $logFilePath,
        ]);

        // Register and login a user
        $user = User::factory()->create();
        Passport::actingAs($user);
    }

    /**
     * Test counting logs with valid filter parameters.
     *
     * @return void
     */
    public function testCountLogsValidFilters()
    {
        // Define the valid filter parameters
        $serviceNames = ['invoice-service'];
        $statusCode = 201;
        $startDate = '2022-09-16';
        $endDate = '2022-09-18';

        // Mock the Cache facade
        Cache::shouldReceive('has')->andReturn(false);
        Cache::shouldReceive('put')->andReturnNull();

        // Act
        $response = $this->json('GET', '/api/logs/count', [
            'serviceNames' => $serviceNames,
            'statusCode' => $statusCode,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        // Assert
        // Assert the response status code
        $response->assertStatus(200);

        // Assert the response structure and content
        $response->assertJsonStructure([
            'data' => [
                'count',
            ],
        ]);

        // Assert the data in the database based on your expectations
        $this->assertDatabaseCount('logs', 20);
    }

    /**
     * Test counting logs with invalid filter parameters.
     *
     * @return void
     */
    public function testCountLogsInvalidFilters()
    {
        // Define invalid filter parameters
        $invalidServiceNames = 'service3';
        $invalidStatusCode = '2023-01-01';
        $invalidStartDate = 200;
        $invalidEndDate = '2023-12-31';

        // Mock the Cache facade
        Cache::shouldReceive('has')->andReturn(false);
        Cache::shouldReceive('put')->andReturnNull();

        // Act
        $response = $this->json('GET', '/api/logs/count', [
            'serviceNames' => $invalidServiceNames,
            'statusCode' => $invalidStatusCode,
            'startDate' => $invalidStartDate,
            'endDate' => $invalidEndDate,
        ]);

        // Assert
        // Assert the response status code
        $response->assertStatus(422);

        // Assert the response structure and content for an invalid response
        // $response->assertJsonStructure([
        //     'error',
        // ]);

        // Assert the data in the database based on your expectations
        $this->assertDatabaseCount('logs', 20);
    }

    // /**
    //  * Test counting logs with database query exception.
    //  *
    //  * @return void
    //  */
    // public function testCountLogsDatabaseQueryException()
    // {
    //     // Define the filter parameters that will trigger a database query exception
    //     $serviceNames = ['service1', 'service2'];
    //     $statusCode = 200;
    //     $startDate = '2022-01-01';
    //     $endDate = '2022-12-31';

    //     // Mock the Cache facade
    //     Cache::shouldReceive('has')->andReturn(false);
    //     Cache::shouldReceive('put')->andReturnNull();

    //     // Mock the Log facade
    //     Log::shouldReceive('error')->andReturnNull();

    //     // Mock the LogModel to throw a QueryException
    //     LogModel::shouldReceive('query')->andThrow(QueryException::class);

    //     // Act
    //     $response = $this->json('GET', '/api/logs/count', [
    //         'serviceNames' => $serviceNames,
    //         'statusCode' => $statusCode,
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //     ]);

    //     // Assert
    //     // Assert the response status code
    //     $response->assertStatus(500);

    //     // Assert the response structure and content for an error response
    //     $response->assertJson([
    //         'error' => 'Database error',
    //     ]);
    // }

    // /**
    //  * Test counting logs with general exception.
    //  *
    //  * @return void
    //  */
    // public function testCountLogsGeneralException()
    // {
    //     // Define the filter parameters that will trigger a general exception
    //     $serviceNames = ['service1', 'service2'];
    //     $statusCode = 200;
    //     $startDate = '2022-01-01';
    //     $endDate = '2022-12-31';

    //     // Mock the Cache facade
    //     Cache::shouldReceive('has')->andReturn(false);
    //     Cache::shouldReceive('put')->andReturnNull();

    //     // Mock the Log facade
    //     Log::shouldReceive('error')->andReturnNull();

    //     // Mock the LogModel to throw a general exception
    //     LogModel::shouldReceive('query')->andThrow(\Exception::class);

    //     // Act
    //     $response = $this->json('GET', '/api/logs/count', [
    //         'serviceNames' => $serviceNames,
    //         'statusCode' => $statusCode,
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //     ]);

    //     // Assert
    //     // Assert the response status code
    //     $response->assertStatus(500);

    //     // Assert the response structure and content for an error response
    //     $response->assertJson([
    //         'error' => 'An error occurred',
    //     ]);
    // }
}
