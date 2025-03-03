<?php

namespace CodeIngestor\Tests;

trait GeneratesImageTrait
{
    protected function generateImage(string $filename): void
    {
        // Create a blank image with white background
        $width = 100;
        $height = 100;
        $image = imagecreate($width, $height);

        // Fill the image with a solid color (e.g., blue)
        $blue = imagecolorallocate($image, 0, 0, 255);
        imagefill($image, 0, 0, $blue);

        // Save the image as a PNG file
        imagepng($image, $filename);
        imagedestroy($image);
    }
}