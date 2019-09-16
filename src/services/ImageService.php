<?php
/**
 * Created by PhpStorm.
 * User: manoj
 * Date: 17/4/19
 * Time: 10:56 PM
 */

namespace Luezoid\Laravelcore\Services;


use Exception;
use Intervention\Image\Facades\Image;

class ImageService {

    /**
     * @param $imageFilePath
     * @param $outputImagePath
     * @return
     * @throws Exception
     */
    public function compressImage($imageFilePath, $outputImagePath = null) {
        if (!file_exists($imageFilePath)) {
            throw new Exception("File does not exist: $imageFilePath");
        }

        $imageExtension = pathinfo($imageFilePath, PATHINFO_EXTENSION);

        $img = Image::make($imageFilePath);
        $originalSize = $img->filesize();
        $tempPath = sys_get_temp_dir() . pathinfo($imageFilePath, PATHINFO_BASENAME);

        if ($outputImagePath) {
            $tempPath = $outputImagePath;
        }

        if ($imageExtension == "png") {
            $this->compressPng($imageFilePath, $tempPath);
        } else {
            if ($imageExtension == "jpg") {
                $img->encode("jpg", 75);
            } else {
                $img->encode($imageExtension);
            }
            $img->save($tempPath);
        }

        $compressedImage = Image::make($tempPath);
        $compressedSize = $compressedImage->filesize();

        if ($compressedSize < $originalSize && is_null($outputImagePath)) {
            copy($tempPath, $imageFilePath);
        }

        return $compressedImage;
    }

    /**
     * Compressing PNG files using PNG-QUANT
     * @param $pathToPngFile
     * @param $compressedFile
     * @param int $minQuality
     * @param int $maxQuality
     * @throws Exception
     */
    function compressPng($pathToPngFile, $compressedFile, $minQuality = 60, $maxQuality = 90) {
        if (!file_exists($pathToPngFile)) {
            throw new Exception("File does not exist: $pathToPngFile");
        }

        $command = "pngquant -f --quality=$minQuality-$maxQuality - < " . escapeshellarg($pathToPngFile) . " -o " . escapeshellarg($compressedFile);

        exec($command, $output, $result);
        if ($result !== 0) {
            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        return;
    }

    /**
     * @param $imageFilePath
     * @param $outputImagePath
     * @param $width
     * @param $height
     * @return
     * @throws Exception
     */
    public function reSizeImage($imageFilePath, $outputImagePath, $width, $height) {
        if (!file_exists($imageFilePath)) {
            throw new Exception("File does not exist: $imageFilePath");
        }
        if (is_null($width) && is_null($height)) {
            throw new Exception("Width and Height cannot be null");
        }

        $img = Image::make($imageFilePath);

        // if width and height is present then resize image to fixed size
        // if width is given, Resize the image to given width and constrain aspect ratio (auto height)
        // if height is given, Resize the image to given height and constrain aspect ratio (auto width)
        if ($width && $height) {
            $img->resize($width, $height);
        } else if ($width) {
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else if ($height) {
            $img->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $img->save($outputImagePath);

        return $img;
    }
}