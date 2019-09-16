<?php

namespace Luezoid\Laravelcore\Files\Services;


use Illuminate\Support\Facades\App;
use Luezoid\Laravelcore\Exceptions\AppException;

class AmazonS3Service
{


    public function saveFileToAmazonServer($filePath, $key, $bucketName, $aclType)
    {
        try {

            $s3 = App::make('aws')->createClient('s3');
            $s3->putObject(array(
                'Bucket' => $bucketName,
                'Key' => $key,
                'ACL' => $aclType,
                'SourceFile' => $filePath,
            ));
            return $key;

        } catch (\Exception $e) {
            throw new AppException($e);
        }
    }


    public function getBucketsList()
    {
        $s3 = App::make('aws')->createClient('s3');
        return $s3->listBuckets();

    }

    public function getTemporaryLink($key, $bucketName, $time)
    {
        $s3 = App::make('aws')->createClient('s3');
        return $s3->getObjectUrl($bucketName, $key, $time);

    }

    public function getSignedUrl($key, $bucketName, $time = null)
    {
        $time = $time ? $time : config('file.aws_temp_link_ime') ?? 15;
        if ($time / (60 * 24 * 7) >= 1) {
            throw new AppException("Time should be less than 7 days");
        }
        $s3 = App::make('aws')->createClient('s3');
        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucketName,
            'Key' => $key
        ]);

        $request = $s3->createPresignedRequest($cmd, "+ $time minutes");

        return (string)$request->getUri();

    }

    public function getObjectUrl($key, $bucketName)
    {
        $s3 = App::make('aws')->createClient('s3');
        return $s3->getObjectUrl(
            $bucketName,
            $key
        );
    }

    public function deleteFile($key, $bucketName)
    {
        $s3 = App::make('aws')->get('s3');
        $result = $s3->deleteObject(array(
            'Bucket' => $bucketName,
            'Key' => $key
        ));
        return $result;
    }
}
