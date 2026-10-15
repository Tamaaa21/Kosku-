<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a fake image file that does not depend on the PHP GD extension.
     */
    protected function createFakeImage(string $filename = 'test.jpg', int $sizeKilobytes = 100): UploadedFile
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === 'png') {
            $header = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        } else {
            $header = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        }

        $paddingLength = max(0, ($sizeKilobytes * 1024) - strlen($header));
        $content = $header.str_repeat('0', $paddingLength);

        return UploadedFile::fake()->createWithContent($filename, $content);
    }
}
