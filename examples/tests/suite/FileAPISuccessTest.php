<?php

namespace Tests\Suite;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
require_once __DIR__.'/../TestCase.php';
require_once __DIR__.'/../../../src/Models/File.php';
require_once __DIR__.'/../../../src/config/file.php';
require_once __DIR__.'/../../../src/Contracts/IFile.php';
require_once __DIR__.'/../../../src/Files/Services/LocalFileUploadService.php';
require_once __DIR__.'/../../../src/Files/Services/SaveFileToS3Service.php';

class FileAPISuccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testFileUpload()
    {
        Storage::fake('local'); // Use a fake disk for testing file uploads

        $file = UploadedFile::fake()->image('test-image.jpg'); // Create a fake test file

        $this->app->bind(\Luezoid\Laravelcore\Contracts\IFile::class, function ($app) {
            if (config('file.is_local')) {
                return $app->make(\Luezoid\Laravelcore\Files\Services\LocalFileUploadService::class);
            }
            return $app->make(\Luezoid\Laravelcore\Files\Services\SaveFileToS3Service::class);
        });

        $response = $this->post('/api/files', [
            'file' => $file,
            'type' => 'EXAMPLE',
        ]);

        $response->assertStatus(200); // Assert that the response has a status code of 200
        // Assert the JSON structure of the response
        $response->assertJsonStructure([
            'message',
            'data' => [
                'type',
                'name',
                'localPath',
                's3Key',
                'updatedAt',
                'createdAt',
                'id',
                'url',
            ],
            'type',
        ]);
    }
}
